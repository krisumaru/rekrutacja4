<?php

declare(strict_types=1);

namespace Rekrutacja4\Test\Repository;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use rekrutacja4\RestClient\Repository\ProducerRepository;
use rekrutacja4\RestClient\Http\ClientInterface;
use rekrutacja4\RestClient\Model\Producer;
use rekrutacja4\RestClient\Exception\ValidationException;
use rekrutacja4\RestClient\Exception\ApiException;
use rekrutacja4\RestClient\Exception\ConflictException;
use rekrutacja4\RestClient\Exception\BadRequestException;

class ProducerRepositoryTest extends TestCase
{
    private const PATH = '/shop_api/v1/producers';

    private function makeResponse(int $status, string $body): ResponseInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn($body);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($status);
        $response->method('getBody')->willReturn($stream);

        return $response;
    }

    public function testCreateOneSuccessReturnsProducerInstance(): void
    {
        $producerArray = [
            'name' => 'Acme',
            'id' => 123,
            'site_url' => 'https:\/\/example.com',
            'logo_filename' => 'logo.png',
            'ordering' => 10,
            'source_id' => 'src-1',
        ];

        $responseBody = json_encode(['producer' => $producerArray]);

        $response = $this->makeResponse(200, $responseBody);

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('post')
            ->with(self::PATH, ['producer' => $producerArray])
            ->willReturn($response);

        $inputProducer = Producer::fromArray($producerArray);

        $repo = new ProducerRepository($client);
        $result = $repo->createOne($inputProducer);

        $this->assertInstanceOf(Producer::class, $result);
    }

    public function testCreateOneThrowsValidationExceptionOnInvalidDataForObject(): void
    {
        $body = json_encode([
            'version' => 'v1',
            'success' => false,
            'data' => null,
            'error' => [
                'reason_code' => 'INVALID_DATA_FOR_OBJECT',
                'messages' => ['Błędna wartość X w polu Y']
            ]
        ]);
        $response = $this->makeResponse(400, $body);

        $client = $this->createMock(ClientInterface::class);
        $client->method('post')->willReturn($response);

        $inputProducer = $this->getSampleProducer();

        $repo = new ProducerRepository($client);

        $this->expectException(ValidationException::class);
        $repo->createOne($inputProducer);
    }

    public function testCreateOneThrowsApiExceptionOnOther400(): void
    {
        $body = json_encode([
            'version' => 'v1',
            'success' => false,
            'data' => null,
            'error' => [
                'reason_code' => 'SOME_OTHER_CODE',
                'messages' => ['Something else']
            ]
        ]);
        $response = $this->makeResponse(400, $body);

        $client = $this->createMock(ClientInterface::class);
        $client->method('post')->willReturn($response);

        $inputProducer = $this->getSampleProducer();

        $repo = new ProducerRepository($client);

        $this->expectException(ApiException::class);
        $repo->createOne($inputProducer);
    }

    public function testCreateOneThrowsConflictExceptionOn409(): void
    {
        $body = json_encode(['message' => 'conflict']);
        $response = $this->makeResponse(409, $body);

        $client = $this->createMock(ClientInterface::class);
        $client->method('post')->willReturn($response);

        $inputProducer = $this->getSampleProducer();

        $repo = new ProducerRepository($client);

        $this->expectException(ConflictException::class);
        $repo->createOne($inputProducer);
    }

    public function testCreateOneThrowsBadRequestExceptionOnOther4xx(): void
    {
        $body = json_encode(['message' => 'unprocessable']);
        $response = $this->makeResponse(422, $body);

        $client = $this->createMock(ClientInterface::class);
        $client->method('post')->willReturn($response);

        $inputProducer = $this->getSampleProducer();

        $repo = new ProducerRepository($client);

        $this->expectException(BadRequestException::class);
        $repo->createOne($inputProducer);
    }

    public function testCreateOneThrowsApiExceptionOnServerErrorOrInvalidStatus(): void
    {
        $body = 'server error';
        $response = $this->makeResponse(500, $body);

        $client = $this->createMock(ClientInterface::class);
        $client->method('post')->willReturn($response);

        $inputProducer = $this->getSampleProducer();

        $repo = new ProducerRepository($client);

        $this->expectException(ApiException::class);
        $repo->createOne($inputProducer);
    }

    public function testCreateOneThrowsApiExceptionOnInvalidJson(): void
    {
        $response = $this->makeResponse(200, '{not: valid json');

        $client = $this->createMock(ClientInterface::class);
        $client->method('post')->willReturn($response);

        $inputProducer = $this->getSampleProducer();

        $repo = new ProducerRepository($client);

        $this->expectException(ApiException::class);
        $repo->createOne($inputProducer);
    }

    public function getSampleProducer(): Producer
    {
        return Producer::fromArray([
            'name' => 'Acme',
            'id' => 123,
            'site_url' => 'https:\/\/example.com',
            'logo_filename' => 'logo.png',
            'ordering' => 10,
            'source_id' => 'src-1',
        ]);
    }
}
