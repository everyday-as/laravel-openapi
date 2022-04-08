<?php

namespace Vyuldashev\LaravelOpenApi\Http;

use GoldSpecDigital\ObjectOrientedOAS\OpenApi;
use Vyuldashev\LaravelOpenApi\Generator;

class OpenApiController
{
    public function __construct(protected Generator $generator) {}

    public function show(string $collection): OpenApi
    {
        return $generator->generate($collection);
    }
}
