<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client as Guzzle;
use rekrutacja4\RestClient\Http\Client\GuzzleClient;
use rekrutacja4\RestClient\Model\Producer;
use rekrutacja4\RestClient\Query\ProducerQuery;
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

// createOne
$created = $repo->createOne(new Producer(
    name: 'Acme Inc',
    id: 1,
    siteUrl: 'https://acme.com',
    logoFilename: 'acme.png',
    ordering: 1,
    sourceId: 'acme',
));
echo 'Created producer: ' . print_r($created, true) . "\n";

// list all
$query = new ProducerQuery($client, $base);
$producers = $query->getAll();
foreach ($producers as $p) {
    echo sprintf("Producer: %s (id: %s)\n", $p->name, $p->id ?? 'n/a');
}
