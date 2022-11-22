<?php

require 'vendor/autoload.php';

use Dkg\Dkg;

$dkg = new Dkg("http://localhost:8904");


$info = $dkg->node()->getInfo();
echo 'Info route';
var_dump($info->getBodyAsArray());
