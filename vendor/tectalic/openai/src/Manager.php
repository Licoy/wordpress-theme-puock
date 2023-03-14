<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi;

use Http\Message\Authentication;
use LogicException;
use Psr\Http\Client\ClientInterface;

/**
 * Holds and interacts with the API Client.
 */
final class Manager
{
    public const BASE_URI = 'https://api.openai.com/v1';

    /** @var Client|null */
    protected static $client = null;

    /**
     * Build the Client.
     *
     * @param ClientInterface $client
     * @param Authentication $auth
     *
     * @return Client
     * @throws LogicException
     */
    public static function build(ClientInterface $client, Authentication $auth): Client
    {
        if (!\is_null(self::$client)) {
            throw new LogicException('Client already built.');
        }
        return self::$client = new Client($client, $auth, self::BASE_URI);
    }

    /**
     * Check if a client globally accessible.
     *
     * @return boolean
     */
    public static function isGlobal(): bool
    {
        return !\is_null(self::$client);
    }

    /**
     * Access the client.
     *
     * @return Client
     * @throws LogicException
     */
    public static function access(): Client
    {
        if (\is_null(self::$client)) {
            throw new LogicException('Client not configured. Use Client::build() first.');
        }
        return self::$client;
    }
}
