<?php

namespace Dkg\Services;

/**
 * Wrapper class for jsonld lib
 */
class JsonLD
{
    /**
     * @param array $assertion
     * @return array
     */
    public static function fromRdf(array $assertion): array
    {
        $assertion = implode("\n", $assertion);

        [$jsonld] = json_decode(json_encode(jsonld_from_rdf($assertion)), true);

        return $jsonld;
    }
}
