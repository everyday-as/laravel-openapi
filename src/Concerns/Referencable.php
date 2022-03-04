<?php

namespace Vyuldashev\LaravelOpenApi\Concerns;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Header;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use InvalidArgumentException;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\CallbackFactory;
use Vyuldashev\LaravelOpenApi\Factories\HeaderFactory;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;
use Vyuldashev\LaravelOpenApi\Factories\SecuritySchemeFactory;

trait Referencable
{
    public static function ref(?string $objectId = null): Schema|Header
    {
        $instance = app(static::class);

        if (!$instance instanceof Reusable) {
            throw new InvalidArgumentException('"' . static::class . '" must implement "' . Reusable::class . '" in order to be referencable.');
        }

        $foreignObjectId = $instance->build()->objectId;

        $baseRef = null;

        if ($instance instanceof CallbackFactory) {
            $baseRef = '#/components/callbacks/';
        } else if ($instance instanceof HeaderFactory) {
            return Header::ref('#/components/headers/' . $foreignObjectId, $objectId);
        } elseif ($instance instanceof ParametersFactory) {
            $baseRef = '#/components/parameters/';
        } elseif ($instance instanceof RequestBodyFactory) {
            $baseRef = '#/components/requestBodies/';
        } elseif ($instance instanceof ResponseFactory) {
            $baseRef = '#/components/responses/';
        } elseif ($instance instanceof SchemaFactory) {
            $baseRef = '#/components/schemas/';
        } elseif ($instance instanceof SecuritySchemeFactory) {
            $baseRef = '#/components/securitySchemes/';
        }

        return Schema::ref($baseRef . $foreignObjectId, $objectId);
    }
}
