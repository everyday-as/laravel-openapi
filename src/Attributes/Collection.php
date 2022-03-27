<?php

namespace Vyuldashev\LaravelOpenApi\Attributes;

use Attribute;
use Vyuldashev\LaravelOpenApi\Contracts\OpenApiAttribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Collection implements OpenApiAttribute
{
    /** @var string|array<string> */
    public array|string $name;

    public function __construct(string|array $name = 'default')
    {
        $this->name = $name;
    }
}
