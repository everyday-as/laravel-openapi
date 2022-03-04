<?php

namespace Vyuldashev\LaravelOpenApi\Factories;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Header;
use Vyuldashev\LaravelOpenApi\Concerns\Referencable;

abstract class HeaderFactory
{
    use Referencable;

    abstract public function build(): Header;
}
