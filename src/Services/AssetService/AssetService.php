<?php

namespace Dkg\Services\AssetService;

use Dkg\Communication\NodeProxyInterface;
use Dkg\Exceptions\InvalidPublishRequestException;
use Dkg\Services\AssertionTools\AssertionTools;
use Dkg\Services\AssetService\Dto\Asset;
use Dkg\Services\AssetService\Dto\PublishOptions;
use Dkg\Services\BlockchainService\BlockchainService;
use Dkg\Services\BlockchainService\BlockchainServiceInterface;
use Dkg\Services\Constants;
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

        $uai = $this->blockchainService->createAsset($asset, $options);
        $asset->setUai($uai);
        $asset->setAssertion($assertion);

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
        $this->validateVisibility($options->getVisibility());

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

    /**
     * @param $visibility
     * @throws InvalidPublishRequestException
     */
    private function validateVisibility($visibility)
    {
        if (!in_array($visibility, [Constants::VISIBILITY_PRIVATE, Constants::VISIBILITY_PUBLIC])) {
            throw new InvalidPublishRequestException("'$visibility' options visibility parameter is not supported.");
        }
    }
}
