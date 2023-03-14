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
use Tectalic\OpenAi\Models\FineTunes\CreateRequest;
use Tectalic\OpenAi\Models\FineTunes\CreateResponse;
use Tectalic\OpenAi\Models\FineTunes\ListResponse;
use Tectalic\OpenAi\Models\FineTunes\RetrieveResponse;
use Tests\MockUri;

final class FineTunesTest extends TestCase
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
        $list = $this->client->fineTunes()->list();
        $response = $list->getResponse();
        $this->assertGreaterThanOrEqual(200, $response->getStatusCode());
        $this->assertLessThan(300, $response->getStatusCode());
        $model = $list->toModel();
        $model->jsonSerialize();
        $this->assertInstanceOf(ListResponse::class, $model);
    }

    public function testCreateMethod(): void
    {
        $create = $this->client->fineTunes()->create(new CreateRequest(['training_file' => 'file-ajSREls59WBbvgSzJSVWxMCB']));
        $response = $create->getResponse();
        $this->assertGreaterThanOrEqual(200, $response->getStatusCode());
        $this->assertLessThan(300, $response->getStatusCode());
        $model = $create->toModel();
        $model->jsonSerialize();
        $this->assertInstanceOf(CreateResponse::class, $model);
    }

    public function testRetrieveMethod(): void
    {
        $retrieve = $this->client->fineTunes()->retrieve('ft-AF1WoRqd3aJAHsqc9NY7iL8F');
        $response = $retrieve->getResponse();
        $this->assertGreaterThanOrEqual(200, $response->getStatusCode());
        $this->assertLessThan(300, $response->getStatusCode());
        $model = $retrieve->toModel();
        $model->jsonSerialize();
        $this->assertInstanceOf(RetrieveResponse::class, $model);
    }
}
