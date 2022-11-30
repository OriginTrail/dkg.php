<?php

namespace Dkg\Services\BlockchainService\Services\AbiManager;

use Dkg\Services\AssertionTools\Util;
use Dkg\Services\BlockchainService\Services\AbiManager\Dto\AbiEvent;

class AbiManager
{
    public const HUB = 'Hub';
    public const SERVICE_AGREEMENT_STORAGE = 'ServiceAgreementStorage';
    public const CONTENT_ASSET = 'ContentAsset';
    public const ASSERTION_REGISTRY = 'AssertionRegistry';
    public const ERC20_TOKEN = 'Token';

    /** @var string[] */
    private $abiMap = [];

    /** @var AbiManager */
    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance(): AbiManager
    {
        if (!self::$instance) {
            self::$instance = new AbiManager();
        }

        return self::$instance;
    }

    /**
     * @param string $abiName
     * @return AbiEvent[]
     */
    public function getAbiEvents(string $abiName): array
    {
        $eventMap = [];
        $abi = $this->getAbi($abiName);
        foreach ($abi as $field) {
            if ($field->type === 'event') {
                $inputTypes = array_map(function ($input) {
                    return $input->internalType;
                }, $field->inputs);


                $eventName = $field->name;
                $fnSignature = $eventName . '(' . implode(',', $inputTypes) . ')';

                $event = new AbiEvent();

                $event->setInputTypes($inputTypes);
                $event->setHash(Util::keccak($fnSignature));

                $eventMap[$eventName] = $event;
            }
        }

        return $eventMap;
    }

    public function getAbi(string $abiName): array
    {
        return json_decode($this->getAbiStringified($abiName));
    }

    public function getAbiStringified(string $abiName): string
    {
        if (!isset($this->abiMap[$abiName])) {
            $this->abiMap[$abiName] = file_get_contents(__DIR__ . "/Abi/$abiName.json");
        }

        return $this->abiMap[$abiName];
    }
}
