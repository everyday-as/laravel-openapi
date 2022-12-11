<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Components;
use Vyuldashev\LaravelOpenApi\Builders\Components\CallbacksBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Components\HeadersBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Components\RequestBodiesBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Components\ResponsesBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Components\SchemasBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Components\SecuritySchemesBuilder;
use Vyuldashev\LaravelOpenApi\Generator;

class ComponentsBuilder
{
    public function __construct(
        protected CallbacksBuilder $callbacksBuilder,
        protected HeadersBuilder $headersBuilder,
        protected RequestBodiesBuilder $requestBodiesBuilder,
        protected ResponsesBuilder $responsesBuilder,
        protected SchemasBuilder $schemasBuilder,
        protected SecuritySchemesBuilder $securitySchemesBuilder
    )
    {
    }

    public function build(
        string $collection = Generator::COLLECTION_DEFAULT,
        array $middlewares = []
    ): ?Components
    {
        $callbacks = $this->callbacksBuilder->build($collection);
        $headers = $this->headersBuilder->build($collection);
        $requestBodies = $this->requestBodiesBuilder->build($collection);
        $responses = $this->responsesBuilder->build($collection);
        $schemas = $this->schemasBuilder->build($collection);
        $securitySchemes = $this->securitySchemesBuilder->build($collection);

        $components = Components::create();

        $hasAnyObjects = false;

        if (!empty($callbacks)) {
            $hasAnyObjects = true;
            $components = $components->callbacks(...$callbacks);
        }

        if (!empty($headers)) {
            $hasAnyObjects = true;
            $components = $components->headers(...$headers);
        }

        if (!empty($requestBodies)) {
            $hasAnyObjects = true;
            $components = $components->requestBodies(...$requestBodies);
        }

        if (!empty($responses)) {
            $hasAnyObjects = true;
            $components = $components->responses(...$responses);
        }

        if (!empty($schemas)) {
            $hasAnyObjects = true;
            $components = $components->schemas(...$schemas);
        }

        if (!empty($securitySchemes)) {
            $hasAnyObjects = true;
            $components = $components->securitySchemes(...$securitySchemes);
        }

        if (!$hasAnyObjects) {
            return null;
        }

        foreach ($middlewares as $middleware) {
            $components = app($middleware)->after($components);
        }

        return $components;
    }
}
