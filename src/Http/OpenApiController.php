<?php

namespace Vyuldashev\LaravelOpenApi\Http;

use GoldSpecDigital\ObjectOrientedOAS\OpenApi;
use Vyuldashev\LaravelOpenApi\Contracts\Generator;

class OpenApiController
{
    protected ?array $cachedSchemas = null;

    public function __construct(protected Generator $generator)
    {
        if ($generator->schemasAreCached()) {
            $this->cachedSchemas = require $generator->getCachedSchemasPath();
        }
    }

    public function show(string $collection): OpenApi
    {
        if ($this->cachedSchemas !== null) {
            return $this->cachedSchemas[$collection];
        }

        return $this->generator->generate($collection);
    }
}
