<?php

namespace UKFast\LaravelApiDocs;

use Attribute;
use ReflectionClass;

#[Attribute]
class Resource implements ResourceSchema
{
    public function toSchema($className)
    {
        $class = new ReflectionClass($className);

        // TODO: Catch ReflectionException and do clearer error handling
        $method = $class->getMethod('schema');
        $comment = $method->getDocComment();
        
        $description = '';

        foreach (explode("\n", $comment) as $line) {

            $line = ltrim($line, ' ');
            $line = ltrim($line, '*');
            $line = ltrim($line, ' ');

            if ($line == "/**" || $line == "/") {
                continue;
            }

            if (strpos($line, '@') !== false) {
                // TODO: @adminOnly
                continue;
            }

            $description .= $line . "\n";
        }

        $description = trim($description);

        $schema = call_user_func([$className, 'schema']);
        $schema = Schema::determineType($schema);
        if ($description) {
            $schema['description'] = $description;
        }
        return $schema;
    }

    public function docsName($class)
    {
        $parts = explode('\\', $class);
        return end($parts);
    }
}
