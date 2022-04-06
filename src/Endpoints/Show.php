<?php

namespace UKFast\LaravelApiDocs\Endpoints;

use UKFast\LaravelApiDocs\Endpoint;
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
