<?php

namespace UKFast\LaravelDataDocs;

interface ResourceSchema
{
    public function toSchema($class);
    public function name($class);
}
