<?php

require_once "vendor/autoload.php";

$client = new \DyRpc\JsonRpc\Client("127.0.0.1", "9200");
$result = $client->call("test_method", ["key"=>"value"]);

var_dump($result);