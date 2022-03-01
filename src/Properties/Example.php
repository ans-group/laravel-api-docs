<?php

namespace UKFast\LaravelDataDocs\Properties;

use Attribute;

#[Attribute]
class Example
{
    public function __construct(
        public $value,
    )
    {
    }
}
