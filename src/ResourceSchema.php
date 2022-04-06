<?php

namespace UKFast\LaravelApiDocs;

interface ResourceSchema
{
    public function toSchema($class);
    public function docsName($class);
}
