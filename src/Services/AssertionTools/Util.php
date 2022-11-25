<?php

namespace Dkg\Services\AssertionTools;

use kornrunner\Solidity;
use Web3\Utils;

class Util
{
    public static function keccak($value): ?string
    {
        return Utils::sha3($value);
    }

    public static function toHex($value): string
    {
        return Solidity::hex($value);
    }
}
