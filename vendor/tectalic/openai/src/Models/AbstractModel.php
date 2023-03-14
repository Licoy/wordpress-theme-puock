<?php

/**
 * Copyright (c) 2022-present Tectalic (https://tectalic.com)
 *
 * For copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * Please see the README.md file for usage instructions.
 */

declare(strict_types=1);

namespace Tectalic\OpenAi\Models;

use JsonSerializable;
use Spatie\DataTransferObject\DataTransferObjectError;
use Spatie\DataTransferObject\FlexibleDataTransferObject;
use stdClass;

abstract class AbstractModel extends FlexibleDataTransferObject implements JsonSerializable
{
    protected const MAPPED = [];
    protected const DEPRECATED = [];
    protected const REQUIRED = [];
    public const FILE_UPLOADS = [];

    public function __construct(array $parameters = [])
    {
        foreach (static::DEPRECATED as $originalName) {
            if (isset($parameters[$originalName])) {
                trigger_error("Property `$originalName` is deprecated", E_USER_DEPRECATED);
            }
        }

        foreach (static::MAPPED as $propertyName => $originalName) {
            if (isset($parameters[$originalName])) {
                $parameters[$propertyName] = $parameters[$originalName];
                unset($parameters[$originalName]);
            }
        }

        $validators = $this->getFieldValidators();

        $valueCaster = $this->getValueCaster();

        foreach ($validators as $field => $validator) {
            // Remove unset properties
            if (!isset($parameters[$field]) && !\in_array($field, static::REQUIRED, true)) {
                unset($this->$field);
                continue;
            }

            if (
                !isset($parameters[$field])
                && !$validator->isNullable
            ) {
                throw DataTransferObjectError::uninitialized(
                    static::class,
                    $field
                );
            }

            $value = $parameters[$field] ?? $this->{$field} ?? null;

            $value = $this->castValue($valueCaster, $validator, $value);

            if (!$validator->isValidType($value)) {
                throw DataTransferObjectError::invalidType(
                    static::class,
                    $field,
                    $validator->allowedTypes,
                    $value
                );
            }

            $this->{$field} = $value;

            unset($parameters[$field]);
        }

        if (!$this->ignoreMissing && count($parameters)) {
            throw DataTransferObjectError::unknownProperties(array_keys($parameters), static::class);
        }
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $obj = new stdClass();
        foreach (\get_object_vars($this) as $varName => $value) {
            if (\in_array($varName, ['ignoreMissing', 'exceptKeys', 'onlyKeys'])) {
                // Skip built-in DTO properties.
                continue;
            }

            if ($value instanceof JsonSerializable) {
                $value = $value->jsonSerialize();
            }
            if (defined('static::MAPPED') && isset(static::MAPPED[$varName])) {
                $obj->{static::MAPPED[$varName]} = $value;
            } else {
                $obj->{$varName} = $value;
            }
        }
        return $obj;
    }
}
