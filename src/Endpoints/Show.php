<?php

namespace UKFast\LaravelDataDocs\Endpoints;

use UKFast\LaravelDataDocs\Endpoint;
use Attribute;

#[Attribute]
class Show extends Endpoint
{
    public function response()
    {
        return [
            'data' => $this->ref($this->args[0]),
            'meta' => (object) [],
        ];
    }
}
