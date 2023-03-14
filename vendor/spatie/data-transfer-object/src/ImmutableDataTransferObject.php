<?php

namespace Spatie\DataTransferObject;

class ImmutableDataTransferObject
{
    /** @var \Spatie\DataTransferObject\DataTransferObject */
    protected $dataTransferObject;

    public function __construct(DataTransferObject $dataTransferObject)
    {
        $this->dataTransferObject = $dataTransferObject;
    }

    public function __set($name, $value)
    {
        throw DataTransferObjectError::immutable($name);
    }

    public function __get($name)
    {
        return $this->dataTransferObject->{$name};
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->dataTransferObject, $name], $arguments);
    }
}
