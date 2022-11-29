<?php

namespace Dkg\Services\AssetService;

use Dkg\Communication\Exceptions\NodeProxyException;
use Dkg\Communication\NodeProxyInterface;
use Dkg\Exceptions\BlockchainException;
use Dkg\Exceptions\HashMismatchException;
use Dkg\Exceptions\InvalidRequestException;
use Dkg\Exceptions\ServiceMisconfigurationException;
use Dkg\Services\AssertionTools\AssertionTools;
use Dkg\Services\AssetService\Dto\Asset;
use Dkg\Services\AssetService\Dto\GetOptions;
use Dkg\Services\AssetService\Dto\GetResult;
use Dkg\Services\AssetService\Dto\PublishOptions;
use Dkg\Services\BlockchainService\BlockchainService;
use Dkg\Services\BlockchainService\BlockchainServiceInterface;
use Dkg\Services\Constants;
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

    // fixme change parameter type to interface
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
    public function create(array $content, ?PublishOptions $options, array $stepHooks = []): Asset
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

        $asset = new Asset();
        $asset->setAssertion($assertion);
        $asset->setAssertionId(AssertionTools::calculateRoot($assertion));
        $asset->setAssertionSize(AssertionTools::getSizeInBytes($assertion));
        $asset->setTriplesCount(AssertionTools::getTriplesCount($assertion));
        $asset->setChunkCount(AssertionTools::getChunkCount($assertion));

        $asset = $this->blockchainService->createAsset($asset, $options);

        $asset->setUai($this->createUai(
            $asset->getBlockchain(),
            $asset->getContract(),
            $asset->getTokenId()
        ));

        $nodeResponse = $this->nodeProxy->publish($asset, $options);

        if (!$nodeResponse->isSuccess()) {
            throw new NodeProxyException(
                "Publish operation failed. OperationId {$nodeResponse->getOperationId()}",
                $nodeResponse
            );
        }

        return $asset;
    }

    /**
     * @throws HashMismatchException
     */
    public function get(string $uai, ?GetOptions $options = null): GetResult
    {
        if (!isset($options)) {
            $options = GetOptions::default();
        }

        [, , $tokenId] = $this->getUaiElements($uai);

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

        if ($options->getOutputFormat() !== Constants::JSONLD_FORMAT_N_QUADS) {
            $assertion = JsonLD::fromRdf($assertion);
        }

        $result = new GetResult();
        $result->setAssertion($assertion);
        $result->setAssertionId($assertionId);
        $result->setNodeResponse($nodeResponse);

        return $result;
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function transfer()
    {
        // TODO: Implement transfer() method.
    }

    public function getOwner()
    {
        // TODO: Implement getOwner() method.
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
