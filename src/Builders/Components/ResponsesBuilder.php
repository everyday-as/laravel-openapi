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
        return $this->getAllClasses($collection)
            ->filter(static function ($class) {
                return
                    is_a($class, ResponseFactory::class, true) &&
                    is_a($class, Reusable::class, true);
            })
            ->map(function ($class) use ($collection) {
                /** @var ResponseFactory $instance */
                $instance = app($class);

                $globalHeaders = Arr::get(config('openapi'), 'collections.' . $collection . '.global_headers', []);

                return optional(
                        $globalHeaders,
                        static fn(array $h) => $instance->build()->headers(...$h)
                    ) ?? $instance->build();
            })
            ->values()
            ->toArray();
    }
}
