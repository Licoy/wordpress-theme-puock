<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi\Models\ImagesEdits;

use Tectalic\OpenAi\Models\AbstractModel;

final class CreateImageRequest extends AbstractModel
{
    /**
     * List of required property names.
     *
     * These properties must all be set when this Model is instantiated.
     */
    protected const REQUIRED = ['prompt', 'image'];

    /** List of properties that represent a file to be uploaded. */
    public const FILE_UPLOADS = ['image', 'mask'];

    /**
     * The image to edit. Must be a valid PNG file, less than 4MB, and square. If mask
     * is not provided, image must have transparency, which will be used as the mask.
     *
     * @var string must be an absolute path to a file.
     */
    public $image;

    /**
     * An additional image whose fully transparent areas (e.g. where alpha is zero)
     * indicate where image should be edited. Must be a valid PNG file, less than 4MB,
     * and have the same dimensions as image.
     *
     * @var string must be an absolute path to a file.
     */
    public $mask;

    /**
     * A text description of the desired image(s). The maximum length is 1000
     * characters.
     *
     * Example: 'A cute baby sea otter wearing a beret'
     *
     * @var string
     */
    public $prompt;

    /**
     * The number of images to generate. Must be between 1 and 10.
     *
     * Default Value: 1
     *
     * Example: 1
     *
     * @var int|null
     */
    public $n;

    /**
     * The size of the generated images. Must be one of 256x256, 512x512, or 1024x1024.
     *
     * Allowed values: '256x256', '512x512', '1024x1024'
     *
     * Default Value: '1024x1024'
     *
     * Example: '1024x1024'
     *
     * @var string|null
     */
    public $size;

    /**
     * The format in which the generated images are returned. Must be one of url or
     * b64_json.
     *
     * Allowed values: 'url', 'b64_json'
     *
     * Default Value: 'url'
     *
     * Example: 'url'
     *
     * @var string|null
     */
    public $response_format;

    /**
     * A unique identifier representing your end-user, which can help OpenAI to monitor
     * and detect abuse. Learn more.
     *
     * Example: 'user-1234'
     *
     * @var string
     */
    public $user;
}
