<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tests\Unit;

use Http\Mock\Client;
use LogicException;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use ReflectionClass;
use ReflectionObject;
use Tectalic\OpenAi\Client as OpenAiClient;
use Tectalic\OpenAi\ClientException;
use Tectalic\OpenAi\Manager;
use Tectalic\OpenAi\Models\AbstractModel;
use Tectalic\OpenAi\Models\AbstractModelCollection;
use Tectalic\OpenAi\Models\UnstructuredModel;
use Tests\NullAuth;
use stdClass;

final class ClientTest extends TestCase
{
    public function setUp(): void
    {
        Manager::build(
            new Client(),
            new NullAuth()
        );
    }

    public function tearDown(): void
    {
        $reflection = new ReflectionClass(Manager::class);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue(null);
        $property->setAccessible(false);
    }

    public function testGlobal(): void
    {
        $client = new OpenAiClient(
            new Client(),
            new NullAuth(),
            Manager::BASE_URI
        );
        $this->assertEquals($client, Manager::access());
        $this->assertTrue(Manager::isGlobal());
        $this->tearDown();
        $this->assertFalse(Manager::isGlobal());
    }

    public function testDoubleBuild(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Client already built.');
        Manager::build(
            new Client(),
            new NullAuth()
        );
    }

    public function testGlobalAccessException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Client not configured. Use Client::build() first.');
        $this->tearDown();
        Manager::access();
    }

    /**
     * @return array<string[]>
     */
    public static function receivingMethods(): array
    {
        return [
            [ 'get' ],
            [ 'delete' ]
         ];
    }

    /**
     * @dataProvider receivingMethods
     */
    public function testReceivingRequests(string $method): void
    {
        $request = Manager::access()->$method('/');
        $this->assertEquals(\strtoupper($method), $request->getMethod());
    }

    public function testRequestWithParam(): void
    {
        $request = Manager::access()->get('/', null, [], ['a' => 'true', 'b' => null, 'c' => [true]]);
        $this->assertEquals('GET', $request->getMethod());
    }

    /**
     * @return array<string[]>
     */
    public static function sendingMethods(): array
    {
        return [
            [ 'post' ]
         ];
    }

    /**
     * @dataProvider sendingMethods
     */
    public function testSendingRequests(string $method): void
    {
        $request = Manager::access()->$method('/');
        $this->assertEquals(\strtoupper($method), $request->getMethod());
    }

    /**
     * @dataProvider sendingMethods
     */
    public function testRequestsHaveUserAgentByDefault(string $method): void
    {
        $request = Manager::access()->$method('/');
        $this->assertTrue($request->hasHeader('User-Agent'));
        $this->assertStringMatchesFormat('Tectalic %s/%d.%d.%d', $request->getHeaderLine('User-Agent'));
    }

    /**
     * @dataProvider sendingMethods
     */
    public function testRequestsCanOverrideDefaultUserAgent(string $method): void
    {
        $request = Manager::access()->$method('/', null, ['User-Agent' => 'Test Client/1.0']);
        $this->assertTrue($request->hasHeader('User-Agent'));
        $this->assertSame('Test Client/1.0', $request->getHeaderLine('User-Agent'));
    }

    public static function jsonBodyEncodeProvider(): array
    {
        return [
            'JSON encode unstructured model with empty object' => [
                new UnstructuredModel((object)[]),
                ['Content-Type' => 'application/json'],
                '{}',
            ],
            'JSON encode unstructured model with empty array' => [
                new UnstructuredModel([]),
                ['Content-Type' => 'application/json'],
                '[]',
            ],
            'JSON encode unstructured model with simple object' => [
                new UnstructuredModel((object)['a' => 'b']),
                ['Content-Type' => 'application/json'],
                '{"a":"b"}',
            ],
            'JSON encode unstructured model with more complex object' => [
                new UnstructuredModel((object)['a' => 123]),
                ['Content-Type' => 'application/json'],
                '{"a":123}',
            ],
            'JSON encode empty collection' => [
                new class () extends AbstractModelCollection {
                },
                ['Content-Type' => 'application/json'],
                '[]',
            ],
            'JSON encode collection containing multiple models' => [
                new class ([
                    new UnstructuredModel((object)['a' => 123]),
                    new UnstructuredModel((object)['a' => 'b']),
                ]) extends AbstractModelCollection {
                },
                ['Content-Type' => 'application/json'],
                '[{"a":123},{"a":"b"}]',
            ],
        ];
    }

    public static function formBodyEncodeProvider(): array
    {
        return [
            'form encode unstructured model with empty object' => [
                new UnstructuredModel((object)[]),
                ['Content-Type' => 'application/x-www-form-urlencoded'],
                '',
            ],
            'form encode unstructured model with empty array' => [
                new UnstructuredModel([]),
                ['Content-Type' => 'application/x-www-form-urlencoded'],
                '',
            ],
            'form encode unstructured model with simple object' => [
                new UnstructuredModel((object)['a' => '1', 'b' => '2']),
                ['Content-Type' => 'application/x-www-form-urlencoded'],
                'a=1&b=2',
            ],
            'form encode unstructured model with more complex object' => [
                new UnstructuredModel((object)['a' => 123]),
                ['Content-Type' => 'application/x-www-form-urlencoded'],
                'a=123',
            ],
            'form encode empty collection' => [
                new class () extends AbstractModelCollection {
                },
                ['Content-Type' => 'application/x-www-form-urlencoded'],
                '',
            ],
            'form encode collection containing multiple models' => [
                new class ([
                    new UnstructuredModel((object)['a' => 123]),
                    new UnstructuredModel((object)['a' => 'b']),
                ]) extends AbstractModelCollection {
                },
                ['Content-Type' => 'application/x-www-form-urlencoded'],
                '0%5Ba%5D=123&1%5Ba%5D=b',
            ],
        ];
    }

    /**
     * @dataProvider jsonBodyEncodeProvider
     * @dataProvider formBodyEncodeProvider
     *
     * @param AbstractModel|AbstractModelCollection $body
     * @param array $headers
     * @param string $expectedResult
     */
    public function testBodyEncode($body, $headers, $expectedResult): void
    {
        $client = Manager::access();
        $reflectionObject = new ReflectionObject($client);
        $reflectionMethod = $reflectionObject->getMethod('encodeBody');
        $reflectionMethod->setAccessible(true);
        $request = new Request('get', 'https://example.com', $headers);
        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $reflectionMethod->invoke($client, $request, $body);
        $this->assertSame($expectedResult, (string) $request->getBody());
    }

    public static function multipartBodyEncodeProvider(): array
    {
        return [
            'multipart unstructured model with empty object' => [
                new UnstructuredModel((object)[]),
                ['Content-Type' => 'multipart/form-data'],
                '--%s--'
            ],
            'multipart unstructured model with an object with string property' => [
                new UnstructuredModel((object)['propertyName' => 'propertyValue']),
                ['Content-Type' => 'multipart/form-data'],
                "--%s\n" .
                "Content-Type: text/plain\n" .
                'Content-Disposition: form-data; name="propertyName"' . "\n" .
                "Content-Length: 13\n" .
                "\n" .
                "propertyValue\n" .
                "--%s--"
            ],
        ];
    }

    /**
     * @dataProvider multipartBodyEncodeProvider
     *
     * @param AbstractModel|AbstractModelCollection $body
     * @param array $headers
     * @param string $expectedBodyFormat
     */
    public function testBodyEncodeWithMultipartRequest($body, $headers, $expectedBodyFormat): void
    {
        $client = Manager::access();
        $reflectionObject = new ReflectionObject($client);
        $reflectionMethod = $reflectionObject->getMethod('encodeBody');
        $reflectionMethod->setAccessible(true);
        $request = new Request('get', 'https://example.com', $headers);
        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $reflectionMethod->invoke($client, $request, $body);
        $this->assertStringMatchesFormat('multipart/form-data; boundary=%s', (string) $request->getHeaderLine('Content-Type'));
        $this->assertStringNotContainsString('boundary=0', (string) $request->getHeaderLine('Content-Type'));
        $this->assertStringMatchesFormat($expectedBodyFormat, (string) $request->getBody());
    }

    public static function invalidMultipartBodyEncodeProvider(): array
    {
        return [
            'unstructured model with an array' => [
                new UnstructuredModel(['a' => []]),
                ['Content-Type' => 'multipart/form-data'],
                'Unable to encode body. Could not serialize data.'
            ],
            'unstructured model with an object that has a property that cannot be converted to a string' => [
                new UnstructuredModel((object)['a' => new stdClass()]),
                ['Content-Type' => 'multipart/form-data'],
                'Unable to convert object value of Tectalic\OpenAi\Models\UnstructuredModel::a to a string.'
            ],
        ];
    }

    /**
     * @dataProvider invalidMultipartBodyEncodeProvider
     *
     * @param AbstractModel|AbstractModelCollection $body
     * @param array $headers
     * @param string $expectedMessage
     */
    public function testInvalidBodyEncodeWithMultipartRequest($body, $headers, $expectedMessage): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage($expectedMessage);
        $client           = Manager::access();
        $reflectionObject = new ReflectionObject($client);
        $reflectionMethod = $reflectionObject->getMethod('encodeBody');
        $reflectionMethod->setAccessible(true);
        $request = new Request('get', 'https://example.com', $headers);
        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $reflectionMethod->invoke($client, $request, $body);
    }

    public function testInvalidJsonBody(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Unable to encode body as JSON: Type is not supported');

        $client = Manager::access();
        $reflectionObject = new ReflectionObject($client);
        $reflectionMethod = $reflectionObject->getMethod('encodeJsonBody');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invoke($client, new UnstructuredModel(\fopen(__FILE__, "r")));
    }

    public function testInvalidFormBody(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Unable to encode body. Could not serialize data.');

        $client = Manager::access();
        $reflectionObject = new ReflectionObject($client);
        $reflectionMethod = $reflectionObject->getMethod('encodeFormBody');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invoke($client, new UnstructuredModel(\fopen(__FILE__, "r")));
    }

    public function testInvalidBodyWithNoContentTypeHeader(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Unable to encode body. Content-Type request header must be set.');
        $handle = fopen(__FILE__, "r");
        Manager::access()->post(
            '/',
            new UnstructuredModel($handle)
        );
    }

    public function testInvalidBodyWithUnsupportedContentType(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Unexpected Content-Type header value: application/test');
        $handle = fopen(__FILE__, "r");
        Manager::access()->post(
            '/',
            new UnstructuredModel($handle),
            ['Content-Type' => 'application/test']
        );
    }

    /**
     * @return array<string[]>
     */
    public static function invalidResponse(): array
    {
        return [
            ['get', ClientException::class, ''],
            ['delete', LogicException::class, 'Unexpected error. See previous exception for details.'],
        ];
    }

    /**
     * @dataProvider invalidResponse
     * @param class-string<\Exception> $exception
     */
    public function testInvalidResponse(string $method, string $exception, string $message): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage($message);

        $mockClient = new Client();
        $mockClient->addException(new $exception());

        $client = new OpenAiClient(
            $mockClient,
            new NullAuth(),
            Manager::BASE_URI
        );
        $client->sendRequest($client->$method('/'));
    }

    public static function mergeRequestPartsProvider(): array
    {
        return [
            'no query params' => [
                new Request('get', 'https://example.com'),
                [],
                'https://example.com',
            ],
            'one query string param' => [
                new Request('get', 'https://example.com'),
                [ 'a' => 'b' ],
                'https://example.com?a=b',
            ],
            'top level bool params' => [
                new Request('get', 'https://example.com'),
                [ 'a' => true, 'b' => false ],
                'https://example.com?a=true&b=false',
            ],
            'second level bool params' => [
                new Request('get', 'https://example.com'),
                [ 'a' => 'string', 'b' => [ 'b1' => true, 'b2' => false ] ],
                'https://example.com?a=string&b%5Bb1%5D=true&b%5Bb2%5D=false',
            ],
            'one additional query string param' => [
                new Request('get', 'https://example.com?a=b'),
                [ 'c' => 'd' ],
                'https://example.com?a=b&c=d',
            ],
            'one additional query string param overwrites an existing URL parameter of the same name' => [
                new Request('get', 'https://example.com?a=b'),
                [ 'a' => 'c' ],
                'https://example.com?a=c',
            ],
        ];
    }

    /**
     * @dataProvider mergeRequestPartsProvider
     *
     * @param RequestInterface $request
     * @param array $queryParams
     * @param string $expectedUri
     */
    public function testMergeRequestParts($request, $queryParams, $expectedUri): void
    {
        $client = Manager::access();
        $reflectionObject = new ReflectionObject($client);
        $reflectionMethod = $reflectionObject->getMethod('mergeRequestParts');
        $reflectionMethod->setAccessible(true);
        $request = $reflectionMethod->invoke($client, $request, [], $queryParams, null);
        $this->assertInstanceOf(RequestInterface::class, $request);
        /** @var RequestInterface $request */
        $this->assertSame($expectedUri, (string) $request->getUri());
    }

    public function testSendRequestWithFileUpload(): void
    {
        $client = Manager::access();
        $reflectionObject = new ReflectionObject($client);
        $reflectionProperty = $reflectionObject->getProperty('fileHandles');
        $reflectionProperty->setAccessible(true);
        $request = new Request('post', 'https://example.com');
        /** @var \Psr\Http\Message\RequestInterface $request */
        $reflectionProperty->setValue($client, [ \fopen(__FILE__, 'r') ]);
        $this->assertCount(1, (array) $reflectionProperty->getValue($client));
        $response = $client->sendRequest($request);
        // The file handle(s) should be closed after the request is sent.
        $this->assertCount(0, (array) $reflectionProperty->getValue($client));
    }

    public function testSendRequestWithFileUploadAndClosedFilePointer(): void
    {
        $client = Manager::access();
        $reflectionObject = new ReflectionObject($client);
        $reflectionProperty = $reflectionObject->getProperty('fileHandles');
        $reflectionProperty->setAccessible(true);
        $request = new Request('post', 'https://example.com');
        /** @var \Psr\Http\Message\RequestInterface $request */
        $reflectionProperty->setValue($client, [ \fopen(__FILE__, 'r') ]);
        // Close file handle.
        $fileHandle = ((array) $reflectionProperty->getValue($client))[0];
        \assert(\is_resource($fileHandle));
        \fclose($fileHandle);
        $this->assertCount(1, (array) $reflectionProperty->getValue($client));
        $response = $client->sendRequest($request);
        // The file handle(s) fclose() error should be gracefully handled.
        $this->assertCount(0, (array) $reflectionProperty->getValue($client));
    }
}
