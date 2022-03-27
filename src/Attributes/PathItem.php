<?php

namespace Vyuldashev\LaravelOpenApi\Attributes;

use Attribute;
use Vyuldashev\LaravelOpenApi\Contracts\OpenApiAttribute;

#[Attribute(Attribute::TARGET_CLASS)]
class PathItem implements OpenApiAttribute
{
}
