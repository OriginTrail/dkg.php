<?php

namespace Dkg\Communication\Enums;

class QueryTypes
{
    public const CONSTRUCT = 'CONSTRUCT';
    public const SELECT = 'SELECT';

    /**
     * Returns all available query types
     * @return string[]
     */
    public static function getAll(): array
    {
        return [self::CONSTRUCT, self::SELECT];
    }
}
