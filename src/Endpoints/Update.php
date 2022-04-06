<?php

namespace UKFast\LaravelDataDocs\Endpoints;

use UKFast\LaravelDataDocs\Endpoint;
use Attribute;

#[Attribute]
class Update extends Endpoint
{
    public function __construct(protected $resource)
    {}

    public function request()
    {
        $this->bodyNotRequired();
        return $this->ref($this->resource);
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
