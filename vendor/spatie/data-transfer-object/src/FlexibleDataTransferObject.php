<?php

namespace Spatie\DataTransferObject;

abstract class FlexibleDataTransferObject extends DataTransferObject
{
    /** @var bool */
    protected $ignoreMissing = true;
}
