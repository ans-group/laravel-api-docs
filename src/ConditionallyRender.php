<?php

namespace UKFast\LaravelApiDocs;

interface ConditionallyRender
{
    public function shouldRender(): bool;
}