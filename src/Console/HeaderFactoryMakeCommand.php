<?php

namespace Vyuldashev\LaravelOpenApi\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class HeaderFactoryMakeCommand extends GeneratorCommand
{
    protected $name = 'openapi:make-header';
    protected $description = 'Create a new Header factory class';
    protected $type = 'Header';

    protected function buildClass($name)
    {
        return str_replace(
            'DummyHeader',
            Str::replaceLast('Header', '', class_basename($name)),
            parent::buildClass($name)
        );
    }

    protected function getStub(): string
    {
        return __DIR__.'/stubs/header.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\OpenApi\Headers';
    }

    protected function qualifyClass($name): string
    {
        $name = parent::qualifyClass($name);

        if (Str::endsWith($name, 'Header')) {
            return $name;
        }

        return $name.'Header';
    }
}