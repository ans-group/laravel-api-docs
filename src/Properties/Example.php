<?php

namespace UKFast\LaravelApiDocs\Properties;

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
