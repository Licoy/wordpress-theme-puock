<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi\Models\Files;

use Tectalic\OpenAi\Models\AbstractModel;

final class CreateRequest extends AbstractModel
{
    /**
     * List of required property names.
     *
     * These properties must all be set when this Model is instantiated.
     */
    protected const REQUIRED = ['file', 'purpose'];

    /** List of properties that represent a file to be uploaded. */
    public const FILE_UPLOADS = ['file'];

    /** @var bool */
    protected $ignoreMissing = false;

    /**
     * Name of the JSON Lines file to be uploaded.
     * If the purpose is set to "fine-tune", each line is a JSON record with "prompt"
     * and "completion" fields representing your training examples.
     *
     * @var string must be an absolute path to a file.
     */
    public $file;

    /**
     * The intended purpose of the uploaded documents.
     * Use "fine-tune" for Fine-tuning. This allows us to validate the format of the
     * uploaded file.
     *
     * Example: 'fine-tune'
     *
     * @var string
     */
    public $purpose;
}
