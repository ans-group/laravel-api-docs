<?php

namespace UKFast\LaravelApiDocs;

class Ref
{
    public function __construct(
        protected string $class
    ) {}

    public function url()
    {
        return "#/components/schemas/{$this->name()}";
    }
    
    public function name()
    {
        $parts = explode("\\", $this->class);
        return str_replace("Data", "", $parts[count($parts) - 1]);
    }
}
