<?php

namespace UKFast\LaravelDataDocs;

use Illuminate\Routing\Route;
use stdClass;

abstract class Endpoint
{
    protected Route $route;

    protected $args;

    protected $contentType = 'application/json';

    protected $required = true;

    protected $statusCode = 200;

    public function __construct(...$args)
    {
        $this->args = $args;
    }

    public function setRoute(Route $route)
    {
        $this->route = $route;
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    public function request()
    {
        return false;
    }

    public function response()
    {
        return false;
    }

    public function dataObjects()
    {
        return [];
    }

    public function toSpec()
    {
        $request = $this->request();
        $response = $this->response();

        $spec = [
            'responses' => [
                $this->statusCode => [
                    'description' => 'Success',
                ]
            ]
        ];

        if ($response) {
            $spec['responses'][$this->statusCode]['content'] = [
                $this->contentType => ['schema' => $this->determineType($response)],
            ];
        }

        if ($request) {
            $spec['requestBody'] = [
                'required' => $this->required,
                'content' => [$this->contentType => ['schema' => $this->determineType($request)]],
            ];
        }

        return $spec;
    }

    protected function ref($class)
    {
        return new Ref($class);
    }

    protected function noContent()
    {
        $this->contentType = 'application/json';
    }

    protected function bodyNotRequired()
    {
        $this->required = false;
    }

    protected function setStatusCode($code)
    {
        $this->statusCode = $code;
    }

    protected function determineType($value)
    {
        if (is_array($value) || $value instanceof stdClass) {
            if (!$value instanceof stdClass && is_numeric(array_keys($value)[0])) {
                $examples = [];
                foreach ($value as $item) {
                    if ($item instanceof Ref) {
                        continue;
                    }
                    $examples[] = $item;
                }
                $spec = [
                    'type' => 'array',
                    'items' => $this->determineType($value[0]),
                ];

                if (!empty($examples)) {
                    $spec['example'] = $examples;
                }

                return $spec;
            }

            $properties = [];
            foreach ($value as $key => $subValue) {
                $properties[$key] = $this->determineType($subValue);
            }

            return [
                'type' => 'object',
                'properties' => $properties,
            ];
        }

        $type = null;
        if (is_string($value)) {
            $type = 'string';
        } else if (is_integer($value)) {
            $type = 'integer';
        } else if ($value instanceof Ref) {
            return ['$ref' => $value->url()];
        } else if (is_bool($value)) {
            $type = 'boolean';
        } else {
            throw new \Exception("Could not determine type");
        }

        return [
            'type' => $type,
            'example' => $value,
        ];
    }
}
