<?php

namespace UKFast\LaravelDataDocs\Endpoints;

use UKFast\LaravelDataDocs\Endpoint;
use Attribute;

#[Attribute]
class Destroy extends Endpoint
{
    public function response()
    {
        $this->setStatusCode(204);
        return null;
    }
}
