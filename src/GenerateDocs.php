<?php

namespace UKFast\LaravelDataDocs;

use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

class GenerateDocs extends Command
{
    protected $signature = 'docs:generate';

    protected $description = 'Generate an openapi.yml file';

    public function handle()
    {
        $spec = OpenApiSpec::fromRoutes();
        $yaml = Yaml::dump($spec->toArray(), 100, 2, Yaml::DUMP_OBJECT_AS_MAP);
        file_put_contents(base_path("openapi.yaml"), $yaml);
    }
}
