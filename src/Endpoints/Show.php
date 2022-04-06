<?php

namespace UKFast\LaravelDataDocs\Endpoints;

use UKFast\LaravelDataDocs\Endpoint;
use Attribute;

#[Attribute]
class Show extends Endpoint
{
    public function __construct(protected $resource)
    {}

    public function response()
    {
        return [
            'data' => $this->ref($this->resource),
            'meta' => (object) [],
        ];
    }
}
