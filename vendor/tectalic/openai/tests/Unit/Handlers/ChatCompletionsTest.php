<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tests\Unit\Handlers;

use Http\Mock\Client;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionObject;
use Tectalic\OpenAi\Authentication;
use Tectalic\OpenAi\ClientException;
use Tectalic\OpenAi\Handlers\ChatCompletions;
use Tectalic\OpenAi\Manager;
use Tectalic\OpenAi\Models\ChatCompletions\CreateRequest;
use Tests\AssertValidateTrait;

final class ChatCompletionsTest extends TestCase
{
    use AssertValidateTrait;

    /** @var Client */
    private $mockClient;

    protected function setUp(): void
    {
        $this->mockClient = new Client();
        Manager::build(
            $this->mockClient,
            new Authentication('token')
        );
    }

    protected function tearDown(): void
    {
        $reflectionClass = new ReflectionClass(Manager::class);
        $reflectionProperty = $reflectionClass->getProperty('client');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue(null);
    }

    public function testMissingRequest(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Request not configured.');
        (new ChatCompletions())->getRequest();
    }

    public function testUnsupportedContentTypeResponse(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Unsupported content type: text/plain');

        $handler = new ChatCompletions();
        $method = (new ReflectionObject($handler))->getMethod('parseResponse');
        $method->setAccessible(true);
        $method->invoke($handler, new Response(
            200,
            ['Content-Type' => 'text/plain'],
            null
        ));
    }

    public function testInvalidJsonResponse(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Failed to parse JSON response body: Syntax error');

        $handler = new ChatCompletions();
        $method = (new ReflectionObject($handler))->getMethod('parseResponse');
        $method->setAccessible(true);
        $method->invoke($handler, new Response(
            200,
            ['Content-Type' => 'application/json'],
            'invalidJson'
        ));
    }

    public function testUnsuccessfulResponseCode(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage("Unsuccessful response. HTTP status code: 418 (I'm a teapot).");

        $handler = new ChatCompletions();
        $property = (new ReflectionObject($handler))->getProperty('response');
        $property->setAccessible(true);
        $property->setValue($handler, new Response(418));
        $handler->toModel();
    }

    public static function toArrayDataProvider(): array
    {
        return [
            ['{}', []],
            ['[]', []],
            ['{"a": "b"}', ['a' => 'b']],
        ];
    }

    /**
     * @dataProvider toArrayDataProvider
     */
    public function testToArray(string $rawJsonResponse, array $expected): void
    {
        $handler = new ChatCompletions();
        $property = (new ReflectionObject($handler))->getProperty('response');
        $property->setAccessible(true);
        $property->setValue($handler, new Response(
            200,
            ['Content-Type' => 'application/json'],
            $rawJsonResponse
        ));
        $this->assertSame($expected, $handler->toArray());
    }

    public function testCreateMethod(): void
    {
        $request = (new ChatCompletions())
            ->create(new CreateRequest([
            'model' => 'alpha0',
            'messages' => [['role' => 'system', 'content' => 'alpha0']],
        ]))
            ->getRequest();
        $this->assertValidate($request);
    }
}
