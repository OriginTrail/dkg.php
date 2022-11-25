<?php

namespace Dkg\Services\BlockchainService;

use Dkg\Services\BlockchainService\Dto\BlockchainConfig;

class BlockchainService implements BlockchainServiceInterface
{

    /**
     * @return string[]
     */
    public static function getAvailableBlockchains(): array
    {
        return array_keys(self::getBlockchainConfigs());
    }

    /**
     * @return BlockchainConfig[]
     */
    private static function getBlockchainConfigs(): array
    {
        $ganache = new BlockchainConfig('http://localhost:7545', '0x209679fA3B658Cd0fC74473aF28243bfe78a9b12');
        $polygon = new BlockchainConfig('https://matic-mumbai.chainstacklabs.com', '0xdaa16AC171CfE8Df6F79C06E7EEAb2249E2C9Ec8');
        $otp = new BlockchainConfig('wss://lofar.origin-trail.network', '0xc9184C1A0CE150a882DC3151Def25075bdAf069C');

        return [
            'ganache' => $ganache,
            'polygon' => $polygon,
            'otp' => $otp
        ];
    }
}
