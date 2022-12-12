<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi;

use Attribute;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Vyuldashev\LaravelOpenApi\Attributes\Parameters;
use Vyuldashev\LaravelOpenApi\Contracts\OpenApiAttribute;

class RouteInformation
{
    public ?string $domain;
    public string $method;
    public string $uri;
    public ?string $name;
    public string $controller;

    public Collection $parameters;

    /** @var Collection|Attribute[] */
    public Collection|array $controllerAttributes;

    public string $action;

    /** @var ReflectionParameter[] */
    public array $actionParameters;

    /** @var Collection|Attribute[] */
    public Collection|array $actionAttributes;

    public ?DocBlock $actionDocBlock;

    /**
     * @param Route $route
     * @return RouteInformation
     *
     * @throws ReflectionException
     */
    public static function createFromRoute(Route $route): RouteInformation
    {
        return tap(new static(), static function (self $instance) use ($route): void {
            $method = collect($route->methods())
                ->map(static fn($value) => Str::lower($value))
                ->filter(static fn($value) => !in_array($value, ['head', 'options'], true))
                ->first();

            $actionNameParts = explode('@', $route->getActionName());

            if (count($actionNameParts) === 2) {
                [$controller, $action] = $actionNameParts;
            } else {
                [$controller] = $actionNameParts;
                $action = '__invoke';
            }

            preg_match_all('/{(.*?)}/', $route->uri, $parameters);
            $parameters = collect($parameters[1]);

            if ($parameters->isNotEmpty()) {
                $parameters = $parameters->map(static fn($parameter) => [
                    'name' => Str::replaceLast('?', '', $parameter),
                    'required' => !Str::endsWith($parameter, '?'),
                ]);
            }

            $reflectionClass = new ReflectionClass($controller);
            $reflectionMethod = $reflectionClass->getMethod($action);

            $docComment = $reflectionMethod->getDocComment();
            $docBlock = $docComment ? DocBlockFactory::createInstance()->create($docComment) : null;

            $controllerAttributes = collect($reflectionClass->getAttributes())
                ->filter(
                    fn(ReflectionAttribute $attribute) =>
                    in_array(OpenApiAttribute::class, class_implements($attribute->getName()), true)
                )
                ->map(fn(ReflectionAttribute $attribute) => $attribute->newInstance());

            $actionAttributes = collect($reflectionMethod->getAttributes())
                ->filter(
                    fn(ReflectionAttribute $attribute) =>
                    in_array(OpenApiAttribute::class, class_implements($attribute->getName()), true)
                )
                ->map(fn(ReflectionAttribute $attribute) => $attribute->newInstance());


            $containsControllerLevelParameter = $controllerAttributes->contains(fn ($value) => $value instanceof Parameters);

            $instance->domain = $route->domain();
            $instance->method = $method;
            $instance->uri = Str::start($route->uri(), '/');
            $instance->name = $route->getName();
            $instance->controller = $controller;
            $instance->parameters = $containsControllerLevelParameter ? collect([]) : $parameters;
            $instance->controllerAttributes = $controllerAttributes;
            $instance->action = $action;
            $instance->actionParameters = $reflectionMethod->getParameters();
            $instance->actionAttributes = $actionAttributes;
            $instance->actionDocBlock = $docBlock;
        });
    }
}
