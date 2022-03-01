<?php

namespace UKFast\LaravelDataDocs\Endpoints;

use UKFast\LaravelDataDocs\Endpoint;
use UKFast\LaravelDataDocs\Ref;
use Attribute;

#[Attribute]
class Create extends Endpoint
{
    public function request()
    {
        return new Ref($this->args[0]);
    }

    public function response()
    {
        $this->setStatusCode(201);

        return [
            'data' => [
                'id' => 1,
            ],
            'meta' => [
                'location' => 'http://some-location',
            ]
        ];
    }
}
