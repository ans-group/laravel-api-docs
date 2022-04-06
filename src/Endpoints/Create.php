<?php

namespace UKFast\LaravelApiDocs\Endpoints;

use UKFast\LaravelApiDocs\Endpoint;
use Attribute;

#[Attribute]
class Create extends Endpoint
{
    public function __construct(protected $resource)
    {}

    public function request()
    {
        return $this->ref($this->resource);
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
