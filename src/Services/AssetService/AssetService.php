<?php

namespace Dkg\Services\AssetService;

use Dkg\Communication\NodeProxyInterface;
use Dkg\Exceptions\InvalidPublishRequestException;
use Dkg\Services\AssertionTools\AssertionTools;
use Dkg\Services\AssetService\Dto\Asset;
use Dkg\Services\AssetService\Dto\PublishOptions;
use Dkg\Services\BlockchainService\BlockchainService;
use Dkg\Services\BlockchainService\BlockchainServiceInterface;
use Exception;

class AssetService implements AssetServiceInterface
{
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
     * @throws InvalidPublishRequestException
     * @throws Exception
     */
    public function create(array $content, ?PublishOptions $options, $stepHooks = [])
    {
        if (!isset($options)) {
            // set default options
            $options = new PublishOptions();
        }

        try {
            $this->validatePublishRequest($content, $options);
            $assertion = AssertionTools::formatAssertion($content);
        } catch (Exception $e) {
            throw new InvalidPublishRequestException($e->getMessage());
        }

        $asset = new Asset();
        $asset->setAssertionId(AssertionTools::calculateRoot($assertion));
        $asset->setAssertionSize(AssertionTools::getSizeInBytes($assertion));
        $asset->setTriplesCount(AssertionTools::getTriplesCount($assertion));
        $asset->setChunkCount(AssertionTools::getChunkCount($assertion));
        $asset->setUai($this->createUai(
            $asset->getBlockchain(),
            $asset->getContract(),
            $asset->getTokenId()
        ));

        $asset = $this->blockchainService->createAsset($asset, $options);

        $this->nodeProxy->publish($asset, $options);
    }

    public function get()
    {
        // TODO: Implement get() method.
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
     * @throws InvalidPublishRequestException
     */
    private function validatePublishRequest(array $content, ?PublishOptions $options)
    {
        $this->validateDatasetSize($content);

        if ($options->getBlockchainConfig()) {
            $this->validateBlockchain($options->getBlockchainConfig()->getBlockchainName());
        }
    }

    /**
     * @throws InvalidPublishRequestException
     */
    private function validateDatasetSize(array $content)
    {
        $contentSize = AssertionTools::getSizeInMb($content);

        if ($contentSize > self::MAX_CONTENT_SIZE_IN_MB) {
            throw new InvalidPublishRequestException("Maximum dataset size exceeded. $contentSize / " . self::MAX_CONTENT_SIZE_IN_MB . "MB");
        }
    }

    /**
     * @throws InvalidPublishRequestException
     */
    private function validateBlockchain($blockchain)
    {
        if (!BlockchainService::isBlockchainSupported($blockchain)) {
            throw new InvalidPublishRequestException("'$blockchain' options blockchain parameter is not supported.");
        }
    }

    private function createUai(?string $blockchain, ?string $contract, ?int $tokenId): string
    {
        return "did:$blockchain:$contract/$tokenId";
    }
}
