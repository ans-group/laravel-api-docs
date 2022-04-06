<?php

namespace UKFast\LaravelDataDocs;

use stdClass;

class Schema
{
    public static function determineType($value)
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
                    'items' => static::determineType($value[0]),
                ];

                if (!empty($examples)) {
                    $spec['example'] = $examples;
                }

                return $spec;
            }

            $properties = [];
            foreach ($value as $key => $subValue) {
                $properties[$key] = static::determineType($subValue);
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