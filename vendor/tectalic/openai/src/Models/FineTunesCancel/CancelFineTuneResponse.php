<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi\Models\FineTunesCancel;

use Tectalic\OpenAi\Models\AbstractModel;

final class CancelFineTuneResponse extends AbstractModel
{
    /**
     * List of required property names.
     *
     * These properties must all be set when this Model is instantiated.
     */
    protected const REQUIRED = [
        'id',
        'object',
        'created_at',
        'updated_at',
        'model',
        'fine_tuned_model',
        'organization_id',
        'status',
        'hyperparams',
        'training_files',
        'validation_files',
        'result_files',
    ];

    /** @var string */
    public $id;

    /** @var string */
    public $object;

    /** @var int */
    public $created_at;

    /** @var int */
    public $updated_at;

    /** @var string */
    public $model;

    /** @var string|null */
    public $fine_tuned_model;

    /** @var string */
    public $organization_id;

    /** @var string */
    public $status;

    /** @var \Tectalic\OpenAi\Models\FineTunesCancel\CancelFineTuneResponseHyperparams */
    public $hyperparams;

    /** @var \Tectalic\OpenAi\Models\FineTunesCancel\CancelFineTuneResponseTrainingFilesItem[] */
    public $training_files;

    /** @var \Tectalic\OpenAi\Models\FineTunesCancel\CancelFineTuneResponseValidationFilesItem[] */
    public $validation_files;

    /** @var \Tectalic\OpenAi\Models\FineTunesCancel\CancelFineTuneResponseResultFilesItem[] */
    public $result_files;

    /** @var \Tectalic\OpenAi\Models\FineTunesCancel\CancelFineTuneResponseEventsItem[] */
    public $events;
}
