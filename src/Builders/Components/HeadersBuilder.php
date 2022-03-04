<?php

namespace Vyuldashev\LaravelOpenApi\Builders\Components;

use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\HeaderFactory;
use Vyuldashev\LaravelOpenApi\Generator;

class HeadersBuilder extends Builder
{
    public function build(string $collection = Generator::COLLECTION_DEFAULT): array
    {
        return $this->getAllClasses($collection)
            ->filter(static function ($class) {
                return
                    is_a($class, HeaderFactory::class, true) &&
                    is_a($class, Reusable::class, true);
            })
            ->map(static function ($class) {
                /** @var HeaderFactory $instance */
                $instance = app($class);

                return $instance->build();
            })
            ->values()
            ->toArray();
    }
}