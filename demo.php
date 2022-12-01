<?php

require 'vendor/autoload.php';

use Dkg\Config\DkgConfig;
use Dkg\Dkg;
use Dkg\Services\AssetService\Dto\PublishOptions;

// Dkg configuration
$dkgConfig = new DkgConfig();
$dkgConfig->getHttpConfig()->setBaseUrl('http://localhost:8904');
$dkgConfig->getBlockchainConfig()->setBlockchainName('ganache');
$dkgConfig->getBlockchainConfig()->setPublicKey('0xd6879c0a03add8cfc43825a42a3f3cf44db7d2b9');
$dkgConfig->getBlockchainConfig()->setPrivateKey('02b39cac1532bef9dba3e36ec32d3de1e9a88f1dda597d3ac6e2130aed9adc4e');

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

echo "\n--- PUBLISH ---\n";

$publishOptions = PublishOptions::default();
$publishOptions->setTokenAmount(7);
// base configuration of HttpConfig and BlockchainConfig can be overriden inside publishOptions
$publishOptions->getBlockchainConfig()->setNumOfRetries(100);
$response = $dkg->asset()->create($dataset, $publishOptions);


echo "Publish succeeded.\n";
echo json_encode([
    'UAI' => $response->getAsset()->getUai(),
    'assertionId' => $response->getAsset()->getAssertionId(),
    'assertion' => json_encode($response->getAsset()->getAssertion())
]);

echo "\n\n----- GET -----\n";

$uai = $response->getAsset()->getUai();

$response = $dkg->asset()->get($uai);
echo "Get succeeded. \n\n";

echo json_encode([
    'assertion' => $response->getAssertion(),
    'assertionId' => $response->getAssertionId(),
    'nodeResponse' => $response->getNodeResponse()
]);

echo "\n\n ------ QUERY ------\n";

$query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> SELECT * WHERE { ?id foaf:name ?name }";
$response = $dkg->graph()->query($query, "SELECT");

echo json_encode($response->getData());
echo "\n";
