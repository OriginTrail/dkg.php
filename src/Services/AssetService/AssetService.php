<?php

namespace Dkg\Services\AssetService;

use Dkg\Exceptions\InvalidPublishRequestException;
use Dkg\Services\AssertionTools\AssertionTools;
use Dkg\Services\AssetService\Dto\PublishOptions;
use Dkg\Services\BlockchainService\BlockchainService;
use Exception;

class AssetService implements AssetServiceInterface
{
    private const MAX_CONTENT_SIZE_IN_MB = 2.5;

    /**
     * @throws InvalidPublishRequestException
     * @throws Exception
     */
    public function create(array $content, ?PublishOptions $options, $stepHooks = [])
    {
        $this->validatePublishRequest($content, $options);

        try {
            $assertion = AssertionTools::formatAssertion($content);
            $assertionId = AssertionTools::calculateRoot($assertion);


        } catch(Exception $e) {
            throw new InvalidPublishRequestException($e->getMessage());
        }
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
        $this->validateBlockchain($options->getBlockchain());
    }

    /**
     * @throws InvalidPublishRequestException
     */
    private function validateDatasetSize(array $content)
    {
        $contentSize = strlen(json_encode($content)) / 1024 / 1024;

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
        if (!in_array($visibility, ['public', 'private'])) {
            throw new InvalidPublishRequestException("'$visibility' options visibility prameter is not supported.");
        }
    }
}
