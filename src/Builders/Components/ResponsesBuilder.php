<?php

namespace Vyuldashev\LaravelOpenApi\Builders\Components;

use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;
use Vyuldashev\LaravelOpenApi\Generator;

class ResponsesBuilder extends Builder
{
    public function build(string $collection = Generator::COLLECTION_DEFAULT): array
    {
        $globalHeaders = config("collections.$collection.global_headers", []);

        return $this->getAllClasses($collection)
            ->filter(static function ($class) {
                return
                    is_a($class, ResponseFactory::class, true) &&
                    is_a($class, Reusable::class, true);
            })
            ->map(function ($class) use ($globalHeaders) {
                /** @var ResponseFactory $instance */
                $instance = app($class);

                $response = $instance->build();

                return $response->headers(...$globalHeaders, ...$response->headers ?? []);
            })
            ->values()
            ->toArray();
    }
}
