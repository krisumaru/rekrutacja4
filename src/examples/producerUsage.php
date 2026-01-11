<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client as Guzzle;
use rekrutacja4\RestClient\Http\Client\GuzzleClient;
use rekrutacja4\RestClient\Model\Producer;
use rekrutacja4\RestClient\Repository\ProducerRepository;

// config
$base = 'http://rekrutacja.localhost:8091/api';
$guzzle = new Guzzle([
    'base_uri' => $base,
    'auth' => ['rest', 'vKTUeyrt1!'],
    'timeout' => 5.0
]);

$client = new GuzzleClient($guzzle);
$repo = new ProducerRepository($client, $base);

// list all
$producers = $repo->getAll();
foreach ($producers as $p) {
    echo sprintf("Producer: %s (id: %s)\n", $p->name, $p->id ?? 'n/a');
}

// create
$new = new Producer('Acme Inc');
$created = $repo->createOne($new);
echo "Created producer id: " . ($created->id ?? 'n/a') . "\n";
