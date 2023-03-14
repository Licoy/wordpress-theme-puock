<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tests\Integration\Handlers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Psr18Client;
use Tectalic\OpenAi\Authentication;
use Tectalic\OpenAi\Client;
use Tectalic\OpenAi\Models\Files\CreateRequest;
use Tectalic\OpenAi\Models\Files\CreateResponse;
use Tectalic\OpenAi\Models\Files\DeleteResponse;
use Tectalic\OpenAi\Models\Files\ListResponse;
use Tectalic\OpenAi\Models\Files\RetrieveResponse;
use Tests\MockUri;
use org\bovigo\vfs\content\LargeFileContent;
use org\bovigo\vfs\vfsStream;

final class FilesTest extends TestCase
{
    /** @var Client */
    public $client;

    public function setUp(): void
    {
        $this->client = new Client(
            new Psr18Client(),
            new Authentication(getenv('OPENAI_CLIENT_TEST_AUTH_TOKEN') ?: 'token'),
            (new MockUri())->base
        );
    }

    public function testListMethod(): void
    {
        $list = $this->client->files()->list();
        $response = $list->getResponse();
        $this->assertGreaterThanOrEqual(200, $response->getStatusCode());
        $this->assertLessThan(300, $response->getStatusCode());
        $model = $list->toModel();
        $model->jsonSerialize();
        $this->assertInstanceOf(ListResponse::class, $model);
    }

    public function testCreateMethod(): void
    {
        $this->markTestSkipped('The default mocking server does not support multipart file upload requests.');

        // @phpstan-ignore-next-line
        $filesystem = vfsStream::setup();
        // Create the file(s) to be uploaded.
        $files = ['file'];
        foreach ($files as $file) {
            vfsStream::newFile($file)
                ->withContent(LargeFileContent::withKilobytes(1))
                ->at($filesystem);
        }
        $create = $this->client->files()->create(new CreateRequest(['file' => 'vfs://root/file', 'purpose' => 'fine-tune']));
        $response = $create->getResponse();
        $this->assertGreaterThanOrEqual(200, $response->getStatusCode());
        $this->assertLessThan(300, $response->getStatusCode());
        $model = $create->toModel();
        $model->jsonSerialize();
        $this->assertInstanceOf(CreateResponse::class, $model);
    }

    public function testRetrieveMethod(): void
    {
        $retrieve = $this->client->files()->retrieve('alpha0');
        $response = $retrieve->getResponse();
        $this->assertGreaterThanOrEqual(200, $response->getStatusCode());
        $this->assertLessThan(300, $response->getStatusCode());
        $model = $retrieve->toModel();
        $model->jsonSerialize();
        $this->assertInstanceOf(RetrieveResponse::class, $model);
    }

    public function testDeleteMethod(): void
    {
        $delete = $this->client->files()->delete('alpha0');
        $response = $delete->getResponse();
        $this->assertGreaterThanOrEqual(200, $response->getStatusCode());
        $this->assertLessThan(300, $response->getStatusCode());
        $model = $delete->toModel();
        $model->jsonSerialize();
        $this->assertInstanceOf(DeleteResponse::class, $model);
    }
}
