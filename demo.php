<?php

require 'vendor/autoload.php';

use Dkg\Config\DkgConfig;
use Dkg\Dkg;
use Dkg\Services\AssetService\Dto\PublishOptions;

// Dkg configuration
$dkgConfig = new DkgConfig();
$dkgConfig->getHttpConfig()->setBaseUrl('');
$dkgConfig->getBlockchainConfig()->setBlockchainName('');
$dkgConfig->getBlockchainConfig()->setPublicKey('');
$dkgConfig->getBlockchainConfig()->setPrivateKey('');
$dkg = new Dkg($dkgConfig);

echo "Info route\n";

$info = $dkg->node()->getInfo();
echo json_encode($info->getBodyAsArray());

$dataset = [
    "@context" => "https://json-ld.org/contexts/person.jsonld",
    "@id" => 'http://dbpedia.org/resource/John_Lennon_' . rand(1, 9999),
    "name" => "John Lennon",
    "born" => "1940-10-09",
    "spouse" => "http://dbpedia.org/resource/Cynthia_Lennon"
];

echo "--- PUBLISH ---\n";

$publishOptions = PublishOptions::default();
$publishOptions->setTokenAmount(7);
// base configuration of HttpConfig and BlockchainConfig can be overriden inside publishOptions
$publishOptions->getBlockchainConfig()->setNumOfRetries(100);
$asset = $dkg->asset()->create($dataset, $publishOptions);


echo "Publish succeeded.\n";
echo json_encode([
    'UAI' => $asset->getUai(),
    'assertionId' => $asset->getAssertionId(),
    'assertion' => json_encode($asset->getAssertion())
]);

