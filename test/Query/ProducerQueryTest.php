<?php

declare(strict_types=1);

namespace rekrutacja4\RestClient\Tests\Query;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use rekrutacja4\RestClient\Exception\ApiException;
use rekrutacja4\RestClient\Http\ClientInterface;
use rekrutacja4\RestClient\Query\ProducerQuery;
use rekrutacja4\RestClient\View\ProducerView;

final class ProducerQueryTest extends TestCase
{
    private const PATH = '/shop_api/v1/producers';

    private function makeResponse(string $json, int $status = 200): ResponseInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn($json);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($status);
        $response->method('getBody')->willReturn($stream);

        return $response;
    }

    public function testGetAllReturnsProducerViewsFromWrapper(): void
    {
        $json = json_encode([
            'producers' => [
                [
                    'id' => 1,
                    'name' => 'Acme',
                    'site_url' => 'https://acme.example',
                    'logo_filename' => 'acme.png',
                    'ordering' => 10,
                    'source_id' => 'src-1',
                ],
            ],
        ]);

        $response = $this->makeResponse($json);

        $client = $this->createMock(ClientInterface::class);
        $client->method('get')->with(self::PATH)->willReturn($response);

        $query = new ProducerQuery($client);
        $result = $query->getAll();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(ProducerView::class, $result[0]);
    }

    public function testGetAllReturnsProducerViewsFromList(): void
    {
        $json = json_encode([
            [
                'id' => 2,
                'name' => 'Beta',
                'site_url' => null,
                'logo_filename' => 'logo.png',
                'ordering' => 20,
                'source_id' => 'src-2',
            ],
        ]);

        $response = $this->makeResponse($json);

        $client = $this->createMock(ClientInterface::class);
        $client->method('get')->with(self::PATH)->willReturn($response);

        $query = new ProducerQuery($client);
        $result = $query->getAll();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(ProducerView::class, $result[0]);
    }

    public function testGetAllThrowsOnNon200Response(): void
    {
        $response = $this->makeResponse('Server error', 500);

        $client = $this->createMock(ClientInterface::class);
        $client->method('get')->with(self::PATH)->willReturn($response);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('API returned unexpected code');

        $query = new ProducerQuery($client);
        $query->getAll();
    }

    public function testGetAllThrowsOnInvalidJson(): void
    {
        $response = $this->makeResponse('not a json');

        $client = $this->createMock(ClientInterface::class);
        $client->method('get')->with(self::PATH)->willReturn($response);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('API returned invalid JSON');

        $query = new ProducerQuery($client);
        $query->getAll();
    }

    public function testGetAllThrowsOnInvalidProducersShape(): void
    {
        $json = json_encode(['producers' => 'this-is-not-an-array']);
        $response = $this->makeResponse($json);

        $client = $this->createMock(ClientInterface::class);
        $client->method('get')->with(self::PATH)->willReturn($response);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid response from API');

        $query = new ProducerQuery($client);
        $query->getAll();
    }
}
