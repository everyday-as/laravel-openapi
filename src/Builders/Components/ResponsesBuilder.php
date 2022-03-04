<?php

namespace Vyuldashev\LaravelOpenApi\Builders\Components;

use Illuminate\Support\Arr;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;
use Vyuldashev\LaravelOpenApi\Generator;

class ResponsesBuilder extends Builder
{
    public function build(string $collection = Generator::COLLECTION_DEFAULT): array
    {
        $globalHeaderFactoryClasses = Arr::get(config('openapi'), 'collections.' . $collection . '.global_headers', []);

        $globalHeaders = Arr::flatten(array_map(
            static fn(string $factoryClass) => app($factoryClass)->build(),
            $globalHeaderFactoryClasses
        ));

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
