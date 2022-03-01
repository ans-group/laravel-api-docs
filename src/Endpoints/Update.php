<?php

namespace UKFast\LaravelDataDocs\Endpoints;

use UKFast\LaravelDataDocs\Endpoint;
use UKFast\LaravelDataDocs\Ref;
use Attribute;

#[Attribute]
class Update extends Endpoint
{
    public function request()
    {
        $this->bodyNotRequired();
        return new Ref($this->args[0]);
    }

    public function response()
    {
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
