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

echo "---- INFO route ----\n";

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
$response = $dkg->asset()->create($dataset, $publishOptions);

echo json_encode([
    'UAI' => $response->getAsset()->getUai(),
    'assertionId' => $response->getAsset()->getAssertionId(),
    'assertion' => json_encode($response->getAsset()->getAssertion())
]);


echo "\n\n----- GET -----\n";

$uai = $response->getAsset()->getUai();

$response = $dkg->asset()->get($uai);

echo json_encode([
    'assertion' => $response->getAssertion(),
    'assertionId' => $response->getAssertionId(),
    'nodeResponse' => $response->getNodeResponse()
]);

echo "\n\n ------ QUERY ------\n";

$query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> SELECT * WHERE { ?id foaf:name ?name }";
$response = $dkg->graph()->query($query, "SELECT");

echo json_encode($response->getData());
echo "\n\n";


echo "\n\n ------ GET OWNER ------\n";

$owner = $dkg->asset()->getOwner($uai);

echo "OWNER: $owner";
echo "\n";


echo "\n\n----- TRANSFER OWNER -----\n";

$response = $dkg->asset()->transfer($uai, '0x34734d828d39ce0B3C8ad22B8578Cd2E3236F277');


$owner = $dkg->asset()->getOwner($uai);

echo "NEW OWNER: $owner";
echo "\n";
