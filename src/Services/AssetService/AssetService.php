<?php

namespace Dkg\Services\AssetService;

use Dkg\Communication\Exceptions\NodeProxyException;
use Dkg\Communication\NodeProxyInterface;
use Dkg\Communication\OperationResult;
use Dkg\Exceptions\BlockchainException;
use Dkg\Exceptions\HashMismatchException;
use Dkg\Exceptions\InvalidRequestException;
use Dkg\Exceptions\ServiceMisconfigurationException;
use Dkg\Services\AssertionTools\AssertionTools;
use Dkg\Services\AssetService\Dto\Asset;
use Dkg\Services\AssetService\Dto\GetOptions;
use Dkg\Services\AssetService\Dto\GetResult;
use Dkg\Services\AssetService\Dto\PublishOptions;
use Dkg\Services\AssetService\Dto\PublishResult;
use Dkg\Services\AssetService\Dto\TransferResult;
use Dkg\Services\BlockchainService\BlockchainService;
use Dkg\Services\BlockchainService\BlockchainServiceInterface;
use Dkg\Services\BlockchainService\Dto\BlockchainConfig;
use Dkg\Services\Params;
use Dkg\Services\JsonLD;
use Exception;
use InvalidArgumentException;

class AssetService implements AssetServiceInterface
{
    private const UAI_REGEX = '/did:([a-z]+):([a-z0-9]+)\/(\d+)/';
    private const MAX_CONTENT_SIZE_IN_MB = 2.5;

    /** @var NodeProxyInterface */
    private $nodeProxy;

    /** @var BlockchainServiceInterface */
    private $blockchainService;

    public function __construct(NodeProxyInterface $nodeProxy, BlockchainServiceInterface $blockchainService)
    {
        $this->nodeProxy = $nodeProxy;
        $this->blockchainService = $blockchainService;
    }

    /**
     * @throws InvalidRequestException
     * @throws NodeProxyException
     * @throws BlockchainException
     * @throws ServiceMisconfigurationException
     * @throws Exception
     */
    public function create(array $content, ?PublishOptions $options = null, array $stepHooks = []): PublishResult
    {
        if (!isset($options)) {
            $options = PublishOptions::default();
        }

        try {
            $this->validatePublishRequest($content, $options);
            $assertion = AssertionTools::formatAssertion($content);
        } catch (Exception $e) {
            throw new InvalidRequestException("Invalid publish request. {$e->getMessage()}");
        }

        $assertionSize = AssertionTools::getSizeInBytes($assertion);

        // if no token amount is set by user
        // read it from the network
        if (!$options->getTokenAmount()) {
            $mergedConfig = $this->blockchainService->getMergedConfig($options->getBlockchainConfig());
            $options->setBlockchainConfig($mergedConfig);
            $bidSuggestion = $this->nodeProxy->getBidSuggestion($assertionSize, $options);
            $options->setTokenAmount($bidSuggestion);
        }

        $asset = new Asset();
        $asset->setAssertion($assertion);
        $asset->setAssertionId(AssertionTools::calculateRoot($assertion));
        $asset->setAssertionSize($assertionSize);
        $asset->setTriplesCount(AssertionTools::getTriplesCount($assertion));
        $asset->setChunkCount(AssertionTools::getChunkCount($assertion));

        $asset = $this->blockchainService->createAsset($asset, $options);

        $asset->setUai($this->createUai(
            $asset->getBlockchain(),
            $asset->getContract(),
            $asset->getTokenId()
        ));

        try {
            $nodeResponse = $this->nodeProxy->publish($asset, $options);

        } catch (NodeProxyException $e) {
            $v = '';
        }

        if (!$nodeResponse->isSuccess()) {
            throw new NodeProxyException(
                "Publish operation failed. OperationId {$nodeResponse->getOperationId()}",
                $nodeResponse
            );
        }

        $result = new PublishResult();
        $result->setAsset($asset);
        $result->setOperationResult($nodeResponse);

        return $result;
    }

    /**
     * @throws HashMismatchException
     */
    public function get(string $uai, ?GetOptions $options = null): GetResult
    {
        if (!$this->validateUai($uai)) {
            throw new InvalidArgumentException("Invalid UAI '$uai'");
        }

        if (!isset($options)) {
            $options = GetOptions::default();
        }

        $tokenId = $this->getTokenId($uai);

        $assertionId = $this->blockchainService->getLatestAssertionId($tokenId, $options->getBlockchainConfig());

        $nodeResponse = $this->nodeProxy->get($uai, $options);

        if (!$nodeResponse->isSuccess()) {
            throw new InvalidArgumentException("UAI '$uai' doesn't exist.");
        }

        $assertion = $nodeResponse->getData()->assertion;

        if ($options->getValidate()) {
            $rootHash = AssertionTools::calculateRoot($assertion);
            if ($assertionId !== $rootHash) {
                throw new HashMismatchException("Expected hash $assertionId but got $rootHash");
            }
        }

        if ($options->getOutputFormat() === Params::JSONLD_FORMAT_NQUADS) {
            $assertion = JsonLD::fromRdf($assertion);
        }

        $result = new GetResult();
        $result->setAssertion($assertion);
        $result->setAssertionId($assertionId);
        $result->setNodeResponse($nodeResponse);

        return $result;
    }

    /**
     * @throws InvalidRequestException
     * @throws NodeProxyException
     * @throws Exception
     */
    public function update(string $uai, array $content, ?PublishOptions $options = null): PublishResult
    {
        if (!$this->validateUai($uai)) {
            throw new InvalidArgumentException("Invalid UAI '$uai'");
        }

        if (!isset($options)) {
            $options = PublishOptions::default();
        }

        try {
            $this->validatePublishRequest($content, $options);
            $assertion = AssertionTools::formatAssertion($content);
        } catch (Exception $e) {
            throw new InvalidRequestException("Invalid publish request. {$e->getMessage()}");
        }

        [$blockchain, $assetContract, $tokenId] = $this->getUaiElements($uai);
        $assertionSize = AssertionTools::getSizeInBytes($assertion);
        $asset = new Asset();
        $asset->setAssertion($assertion);
        $asset->setAssertionId(AssertionTools::calculateRoot($assertion));
        $asset->setAssertionSize($assertionSize);
        $asset->setTriplesCount(AssertionTools::getTriplesCount($assertion));
        $asset->setChunkCount(AssertionTools::getChunkCount($assertion));
        $asset->setBlockchain($blockchain);
        $asset->setContract($assetContract);
        $asset->setTokenId($tokenId);

        $bidSuggestion = $this->nodeProxy->getBidSuggestion($assertionSize, $options);

        $this->blockchainService->updateAsset($asset, $options);

        $nodeResponse = $this->nodeProxy->publish($asset, $options);

        if (!$nodeResponse->isSuccess()) {
            throw new NodeProxyException(
                "Publish operation failed. OperationId {$nodeResponse->getOperationId()}",
                $nodeResponse
            );
        }

        $result = new PublishResult();
        $result->setAsset($asset);
        $result->setOperationResult($nodeResponse);

        return $result;
    }

    public function transfer(string $uai, string $toAddress, ?BlockchainConfig $blockchainConfig = null): TransferResult
    {
        if (!$this->validateUai($uai)) {
            throw new InvalidArgumentException("Invalid UAI $uai");
        }

        $tokenId = $this->getTokenId($uai);

        $this->blockchainService->transferAsset($tokenId, $toAddress, $blockchainConfig);
        $owner = $this->getOwner($uai, $blockchainConfig);

        $result = new TransferResult();
        $result->setUai($uai);
        $result->setOwner($owner);
        $result->setOperationResult(new OperationResult('COMPLETED'));

        return $result;
    }

    public function getOwner(string $uai, ?BlockchainConfig $config = null): ?string
    {
        if (!$this->validateUai($uai)) {
            throw new InvalidArgumentException("Invalid UAI. $uai");
        }

        $tokenId = $this->getTokenId($uai);

        return $this->blockchainService->getAssetOwner($tokenId, $config);
    }

    /**
     * @throws InvalidRequestException
     */
    private function validatePublishRequest(array $content, ?PublishOptions $options)
    {
        $this->validateDatasetSize($content);

        if (!$options->validate()) {
            throw new InvalidRequestException('Some of publish options fields are missing.');
        }

        if ($options->getBlockchainConfig()) {
            $this->validateBlockchain($options->getBlockchainConfig()->getBlockchainName());
        }
    }

    /**
     * @throws InvalidRequestException
     */
    private function validateDatasetSize(array $content)
    {
        $contentSize = AssertionTools::getSizeInMb($content);

        if ($contentSize > self::MAX_CONTENT_SIZE_IN_MB) {
            throw new InvalidRequestException("Maximum dataset size exceeded. $contentSize / " . self::MAX_CONTENT_SIZE_IN_MB . "MB");
        }
    }

    /**
     * @throws InvalidRequestException
     */
    private function validateBlockchain($blockchain)
    {
        if (!BlockchainService::isBlockchainSupported($blockchain)) {
            throw new InvalidRequestException("'$blockchain' options blockchain parameter is not supported.");
        }
    }

    private function createUai(?string $blockchain, ?string $contract, ?int $tokenId): string
    {
        return "did:$blockchain:$contract/$tokenId";
    }

    private function validateUai(string $uai): bool
    {
        return !empty($this->getUaiElements($uai));
    }

    private function getTokenId(string $uai): ?string
    {
        $elements = $this->getUaiElements($uai);
        if (!$elements) {
            return null;
        }

        [, , $tokenId] = $elements;

        return $tokenId;
    }

    /**
     * @param string $uai
     * @return array|null [blockchain, contractId, tokenId]
     */
    private function getUaiElements(string $uai): ?array
    {
        $uai = strtolower($uai);
        $elements = [];

        preg_match(self::UAI_REGEX, $uai, $elements);

        if (!count($elements)) {
            return null;
        }

        return [$elements[1], $elements[2], (int)$elements[3]];
    }
}
