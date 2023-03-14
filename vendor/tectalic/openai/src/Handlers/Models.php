<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi\Handlers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tectalic\OpenAi\Client;
use Tectalic\OpenAi\ClientException;
use Tectalic\OpenAi\Manager;
use Tectalic\OpenAi\Models\AbstractModel;
use Tectalic\OpenAi\Models\AbstractModelCollection;
use Tectalic\OpenAi\Models\Models\DeleteResponse;
use Tectalic\OpenAi\Models\Models\ListResponse;
use Tectalic\OpenAi\Models\Models\RetrieveResponse;
use Throwable;

final class Models
{
    /** @var Client */
    private $client;

    /** @var RequestInterface|null */
    private $request = null;

    /** @var ResponseInterface|null */
    private $response = null;

    /** @var class-string<AbstractModel|AbstractModelCollection> */
    private $modelType;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? Manager::access();
    }

    /**
     * Lists the currently available models, and provides basic information about each
     * one such as the owner and availability.
     *
     * Operation URL: GET /models
     * Operation ID:  listModels
     *
     * @api
     * @return self
     */
    public function list(): self
    {
        $url = '/models';
        $this->setRequest($this->client->get(
            $url,
            null,
            []
        ));
        $this->modelType = ListResponse::class;
        return $this;
    }

    /**
     * Retrieves a model instance, providing basic information about the model such as
     * the owner and permissioning.
     *
     * Operation URL: GET /models/{model}
     * Operation ID:  retrieveModel
     *
     * @param string $model The ID of the model to use for this request
     *
     * @api
     * @return self
     */
    public function retrieve($model): self
    {
        $url = sprintf('/models/%s', $model);
        $this->setRequest($this->client->get(
            $url,
            null,
            []
        ));
        $this->modelType = RetrieveResponse::class;
        return $this;
    }

    /**
     * Delete a fine-tuned model. You must have the Owner role in your organization.
     *
     * Operation URL: DELETE /models/{model}
     * Operation ID:  deleteModel
     *
     * @param string $model The model to delete
     *
     * @api
     * @return self
     */
    public function delete($model): self
    {
        $url = sprintf('/models/%s', $model);
        $this->setRequest($this->client->delete(
            $url,
            null,
            []
        ));
        $this->modelType = DeleteResponse::class;
        return $this;
    }

    /**
     * Convert response body to an array.
     *
     * @return array
     * @throws ClientException
     */
    private function parseResponse(ResponseInterface $response): array
    {
        $contentType = \strtolower($response->getHeaderLine('Content-Type'));
        if (substr($contentType, 0, 16) !== 'application/json' && \strlen($contentType) !== 0) {
            throw new ClientException(\sprintf('Unsupported content type: %s', $contentType));
        }

        $body = (string) $response->getBody();
        $body = \strlen($body) === 0 ? '[]' : $body;
        $data = (array) \json_decode($body, true);

        if (\json_last_error()) {
            throw new ClientException(
                'Failed to parse JSON response body: ' . \json_last_error_msg()
            );
        }
        return $data;
    }

    /**
     * Sets the PSR 7 Response object.
     * Also removes the previous response.
     *
     * @param  RequestInterface  $request
     *
     * @return void
     */
    private function setRequest(RequestInterface $request): void
    {
        $this->request = $request;
        $this->response = null;
    }

    /**
     * Returns the PSR 7 Response object.
     *
     * @internal
     * @return RequestInterface
     * @throws ClientException
     */
    public function getRequest(): RequestInterface
    {
        if (\is_null($this->request)) {
            throw new ClientException('Request not configured.');
        }
        return $this->request;
    }

    /**
     * Returns the PSR 7 Response object.
     *
     * @api
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        if (!\is_null($this->response)) {
            return $this->response;
        }

        $this->response = $this->client->sendRequest($this->getRequest());
        $this->request = null;
        return $this->response;
    }

    /**
     * Returns the response body as a model/DTO.
     *
     * @api
     * @return AbstractModel|AbstractModelCollection
     * @throws ClientException if an unsuccessful HTTP response occurs.
     * @throws ClientException if the response body cannot be parsed.
     */
    public function toModel(): object
    {
        if ($this->getResponse()->getStatusCode() < 200 || $this->getResponse()->getStatusCode() >= 300) {
            throw new ClientException(
                \sprintf(
                    'Unsuccessful response. HTTP status code: %s (%s).',
                    $this->getResponse()->getStatusCode(),
                    $this->getResponse()->getReasonPhrase()
                )
            );
        }
        $class = $this->modelType;
        try {
            return new $class($this->parseResponse($this->getResponse()));
        } catch (Throwable $e) {
            throw new ClientException(
                'Response body parse failed. See previous exception for details.',
                0,
                $e
            );
        }
    }

    /**
     * Returns the response body as an associative array.
     *
     * @api
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return $this->parseResponse($this->getResponse());
    }
}
