<?php

namespace UKFast\LaravelApiDocs\Endpoints;

use UKFast\LaravelApiDocs\Endpoint;
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
