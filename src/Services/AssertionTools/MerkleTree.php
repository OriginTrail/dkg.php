<?php

namespace Dkg\Services\AssertionTools;

use Closure;
use Exception;
use Dkg\Services\AssertionTools\Infrastructure\FixedSizeTree;

class MerkleTree
{
    /**
     * @throws Exception
     */
    public static function getRoot($assertion)
    {
        sort($assertion);

        $leaves = self::getLeaves($assertion);

        $tree = new FixedSizeTree(count($leaves), self::hasher(), self::getMerkleOptions());

        foreach ($leaves as $index => $leaf) {
            $tree->set($index, $leaf);
        }

        return $tree->hash();
    }

    /**
     * @throws Exception
     */
    private static function hasher(): Closure
    {
        return function ($data) {
            /**
             * Hash function is also execute within tree->set method.
             * Since leaves are already hashed in a special way, we just
             * want to trim the leaf_ prefix which indicates that they are leaves
             */
            if (substr($data, 0, 5) === "leaf_") {
                return substr($data, 5);
            }

            return Util::keccak($data);
        };
    }

    /**
     * Returns leaves in 0x_ format
     * Because of OT challenge mechanism, leaves are calculated
     * As keccak('keccak(triple) + hex(index)') hash
     * @param $assertion
     * @return array
     */
    private static function getLeaves($assertion): array
    {
        $leaves = [];

        foreach ($assertion as $index => $value) {
            $valueHash = Util::keccak($value);
            $indexHex = Util::toHex($index);
            $compact = $valueHash . $indexHex;

            $leaves[] = 'leaf_' . Util::keccak($compact);
        }

        return $leaves;
    }

    private static function getMerkleOptions(): object
    {
        return (object) [
            'sortPairs' => true
        ];
    }
}
