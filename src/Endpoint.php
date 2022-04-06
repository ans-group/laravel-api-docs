<?php

namespace UKFast\LaravelApiDocs;

use Illuminate\Routing\Route;
use stdClass;

abstract class Endpoint
{
    protected Route $route;

    protected $contentType = 'application/json';

    protected $required = true;

    protected $statusCode = 200;

    protected $referencedObjects = [];

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
        return $this->referencedObjects;
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
                $this->contentType => ['schema' => Schema::determineType($response)],
            ];
        }

        if ($request) {
            $spec['requestBody'] = [
                'required' => $this->required,
                'content' => [$this->contentType => ['schema' => Schema::determineType($request)]],
            ];
        }

        return $spec;
    }

    protected function ref($class)
    {
        $this->referencedObjects[] = $class;
        return new Ref($class);
    }

    protected function bodyNotRequired()
    {
        $this->required = false;
    }

    protected function setStatusCode($code)
    {
        $this->statusCode = $code;
    }
}
