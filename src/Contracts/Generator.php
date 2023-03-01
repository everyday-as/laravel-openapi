<?php

namespace Vyuldashev\LaravelOpenApi\Contracts;

use GoldSpecDigital\ObjectOrientedOAS\OpenApi;

interface Generator
{
    public const COLLECTION_DEFAULT = 'default';

    public function generate(string $collection = self::COLLECTION_DEFAULT): OpenApi;
}
