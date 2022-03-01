<?php

namespace UKFast\LaravelDataDocs;

use UKFast\LaravelDataDocs\Properties\Example;
use UKFast\LaravelDataDocs\Properties\OnlyRead;
use UKFast\LaravelDataDocs\Properties\OnlyWrite;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use ReflectionClass;

class OpenApiSpec
{
    public function __construct(
        protected Collection $endpoints,
        protected Collection $dataObjects,
    )
    {
    }

    public function toArray()
    {
        $spec = static::baseSpec();

        foreach ($this->endpoints as $endpoint) {
            $path = "/" . $endpoint->getRoute()->uri();

            if (!isset($spec['paths'][$path])) {
                $spec['paths'][$path] = [];
            }

            $method = strtolower($endpoint->getRoute()->methods()[0]);
            $spec['paths'][$path][$method] = $endpoint->toSpec();

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

        foreach ($this->dataObjects as $dataObject) {
            $properties = [];
            $reflection = new ReflectionClass($dataObject);
            foreach ($reflection->getConstructor()->getParameters() as $param) {
                $type = $param->getType()->getName();
                $type = match ($type) {
                    'int' => 'integer',
                    default => $type,
                };
                $properties[$param->getName()] = [
                    'type' => $type,
                ];

                if (!empty($param->getAttributes(OnlyWrite::class))) {
                    $properties[$param->getName()]['writeOnly'] = true;
                }

                if (!empty($param->getAttributes(OnlyRead::class))) {
                    $properties[$param->getName()]['readOnly'] = true;
                }

                if (!empty($param->getAttributes(Example::class))) {
                    $value = $param->getAttributes(Example::class)[0]->newInstance()->value;
                    $properties[$param->getName()]['example'] = $value;
                }
            }

            $ref = new Ref($dataObject);

            $spec['components']['schemas'][$ref->name()] = [
                'type' => 'object',
                'properties' => $properties,
            ];
        }

        return $spec;
    }

    public static function fromRoutes(): static
    {
        $endpoints = static::discoverEndpoints();
        $dataObjects = $endpoints->reduce(
            fn ($objs, $ep) => $objs->merge(Arr::wrap($ep->dataObjects())),
            new Collection
        );

        return new static($endpoints, $dataObjects->unique());
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