<?php

namespace UKFast\LaravelDataDocs;

interface ConditionallyRender
{
    public function shouldRender(): bool;
}