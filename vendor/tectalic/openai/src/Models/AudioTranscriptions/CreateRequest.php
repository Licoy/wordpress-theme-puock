<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi\Models\AudioTranscriptions;

use Tectalic\OpenAi\Models\AbstractModel;

final class CreateRequest extends AbstractModel
{
    /**
     * List of required property names.
     *
     * These properties must all be set when this Model is instantiated.
     */
    protected const REQUIRED = ['file', 'model'];

    /** List of properties that represent a file to be uploaded. */
    public const FILE_UPLOADS = ['file'];

    /** @var bool */
    protected $ignoreMissing = false;

    /**
     * The audio file to transcribe, in one of these formats: mp3, mp4, mpeg, mpga,
     * m4a, wav, or webm.
     *
     * @var string must be an absolute path to a file.
     */
    public $file;

    /**
     * ID of the model to use. Only whisper-1 is currently available.
     *
     * @var string
     */
    public $model;

    /**
     * An optional text to guide the model's style or continue a previous audio
     * segment. The prompt should match the audio language.
     *
     * @var string
     */
    public $prompt;

    /**
     * The format of the transcript output, in one of these options: json, text, srt,
     * verbose_json, or vtt.
     *
     * Default Value: 'json'
     *
     * @var string
     */
    public $response_format;

    /**
     * The sampling temperature, between 0 and 1. Higher values like 0.8 will make the
     * output more random, while lower values like 0.2 will make it more focused and
     * deterministic. If set to 0, the model will use log probability to automatically
     * increase the temperature until certain thresholds are hit.
     *
     * Default Value: 0
     *
     * @var float|int
     */
    public $temperature;

    /**
     * The language of the input audio. Supplying the input language in ISO-639-1
     * format will improve accuracy and latency.
     *
     * @var string
     */
    public $language;
}
