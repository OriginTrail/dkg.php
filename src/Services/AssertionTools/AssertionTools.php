<?php

namespace Dkg\Services\AssertionTools;

use Exception;

class AssertionTools
{
    private const ALGORITHM = 'URDNA2015';
    private const FORMAT = 'application/nquads';

    /**
     * @throws Exception
     */
    public static function formatAssertion(array $jsonld): array
    {
        $normalized = jsonld_normalize(
            (object)$jsonld, array('algorithm' => self::ALGORITHM, 'format' => self::FORMAT));

        $assertion = array_filter(explode("\n", $normalized), function ($el) {
            return !empty($el);
        });

        if (empty($assertion)) {
            throw new Exception("File format is corrupted, no n-quads are extracted.");
        }

        return $assertion;
    }

    /**
     * @throws Exception
     */
    public static function calculateRoot(array $assertion)
    {
        return MerkleTree::getRoot($assertion);
    }
}
