<?php

namespace UKFast\LaravelApiDocs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use ReflectionClass;

class OpenApiSpec
{
    public function __construct(
        protected Collection $endpoints
    )
    {
    }

    public function toArray()
    {
        $spec = static::baseSpec();

        $resources = new Collection;

        foreach ($this->endpoints as $endpoint) {
            if ($endpoint instanceof ConditionallyRender) {
                if (!$endpoint->shouldRender()) {
                    continue;
                }
            }
            $path = "/" . $endpoint->getRoute()->uri();

            if (!isset($spec['paths'][$path])) {
                $spec['paths'][$path] = [];
            }

            $method = strtolower($endpoint->getRoute()->methods()[0]);
            $spec['paths'][$path][$method] = $endpoint->toSpec();
            $resources = $resources->merge($endpoint->dataObjects());

            foreach ($endpoint->getRoute()->parameterNames() as $param) {
                if (!isset($spec['paths'][$path][$method]['parameters'])) {
                    $spec['paths'][$path][$method]['parameters'] = [];
                }

                $spec['paths'][$path][$method]['parameters'][] = [
                    'in' => 'path',
                    'name' => $param,
                    'schema' => ['type' => 'string'],
                    'required' => true,
                ];
            }
        }

        foreach ($resources as $dataObject) {
            $properties = [];
            $reflection = new ReflectionClass($dataObject);
            foreach ($reflection->getAttributes() as $attribute) {
                $inst = $attribute->newInstance();
                if ($inst instanceof ConditionallyRender) {
                    if (!$inst->shouldRender()) {
                        continue;
                    }
                }
                if ($inst instanceof ResourceSchema) {
                    $properties = $inst->toSchema($dataObject);
                    $name = $inst->name($dataObject);
                    $spec['components']['schemas'][$name] = $properties;
                }
            }
        }

        return $spec;
    }

    public static function fromRoutes(): static
    {
        $endpoints = static::discoverEndpoints();
        return new static($endpoints);
    }

    public static function baseSpec()
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'version' => '1.0.0',
                'title' => config('docs.name', config('app.name'))
            ],
            'servers' => [[
                'url' => config('docs.url', config('app.url', 'http://example.com'))
            ]],
            'security' => [[
                'api_key' => ['api_key']
            ]],
            'components' => [
                'schemas' => [],
                'securitySchemes' => [
                    'api_key' => [
                        'type' => 'apiKey',
                        'description' => 'API key authentication',
                        'in' => 'header',
                        'name' => 'Authorization',
                    ]
                ],
            ],
            'security' => [
                ['api_key' => ['api_key']]
            ],
            'paths' => [],
        ];
    }

    protected static function discoverEndpoints()
    {
        $routes = Route::getRoutes()->getRoutes();
        $endpoints = new Collection;
        foreach ($routes as $route) {
            $action = $route->action;
            if (!isset($action['controller'])) {
                continue;
            }

            $parts = explode("@", $action['controller']);
            $controller = $parts[0];
            $action = $parts[1] ?? null;

            $reflection = new ReflectionClass($controller);
            foreach ($reflection->getMethods() as $method) {
                if ($method->getName() != $action) {
                    continue;
                }
                
                foreach ($method->getAttributes() as $attr) {
                    $endpoint = $attr->newInstance();
                    if ($endpoint instanceof Endpoint) {
                        $endpoint->setRoute($route);
                        $endpoints->push($endpoint);
                    }
                }
            }
        }

        return $endpoints;
    }
}
