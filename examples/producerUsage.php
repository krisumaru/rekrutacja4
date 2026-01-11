<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use rekrutacja4\RestClient\Http\Client;
use rekrutacja4\RestClient\Model\Producer;
use rekrutacja4\RestClient\Query\ProducerQuery;
use rekrutacja4\RestClient\Repository\ProducerRepository;

// config by env vars, you can also put those in the .env file
putenv('REKRUTACJA4_API_BASE_URI=http://rekrutacja.localhost:8091/api');
putenv('REKRUTACJA4_API_AUTH_USER=rest');
putenv('REKRUTACJA4_API_AUTH_PASSWORD=vKTUeyrt1!');
putenv('REKRUTACJA4_API_TIMEOUT=5.0');

$client = Client::create();
$repo = new ProducerRepository($client);

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
$query = new ProducerQuery($client);
$producers = $query->getAll();
foreach ($producers as $p) {
    echo sprintf("Producer: %s (id: %s): %s\n", $p->name, $p->id, print_r($p->toArray(), true));
}
