<?php

namespace UKFast\LaravelDataDocs\Endpoints;

use UKFast\LaravelDataDocs\Endpoint;
use Attribute;

#[Attribute]
class Index extends Endpoint
{
    public function response()
    {
        return [
            'data' => [$this->ref($this->args[0])],
            'links' => [
                [
                    'url' => $this->route->uri() . '?page=1',
                    'label' => 'Previous',
                    'active' => false,
                ],
                [
                    'url' => $this->route->uri() . '?page=1',
                    'label' => '1',
                    'active' => true,
                ],
                [
                    'url' => $this->route->uri() . '?page=2',
                    'label' => '2',
                    'active' => true,
                ],
                [
                    'url' => $this->route->uri() . '?page=2',
                    'label' => 'Next',
                    'active' => true,
                ]
            ],
            'meta' => [
                'current_page' => 1,
                'first_page_url' => $this->route->uri() . '?page=1',
                'from' => 1,
                'last_page' => 2,
                'last_page_url' => $this->route->uri() . '?page=1',
                'next_page_url' => $this->route->uri() . '?page=1',
                'path' => $this->route->uri(),
                'per_page' => 15,
                'prev_page_url' => $this->route->uri() . '?page=1',
                'to' => 1,
                'total' => 2,
            ]
        ];
    }

    public function dataObjects()
    {
        return [$this->args[0]];
    }
}
