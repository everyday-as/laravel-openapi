<?php

namespace Vyuldashev\LaravelOpenApi;

use GoldSpecDigital\ObjectOrientedOAS\OpenApi;
use Illuminate\Support\Arr;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use Vyuldashev\LaravelOpenApi\Builders\ComponentsBuilder;
use Vyuldashev\LaravelOpenApi\Builders\InfoBuilder;
use Vyuldashev\LaravelOpenApi\Builders\PathsBuilder;
use Vyuldashev\LaravelOpenApi\Builders\ServersBuilder;
use Vyuldashev\LaravelOpenApi\Builders\TagsBuilder;

class Generator implements Contracts\Generator
{
    public string $version = OpenApi::OPENAPI_3_0_2;

    public function __construct(
        protected array             $config,
        protected InfoBuilder       $infoBuilder,
        protected ServersBuilder    $serversBuilder,
        protected TagsBuilder       $tagsBuilder,
        protected PathsBuilder      $pathsBuilder,
        protected ComponentsBuilder $componentsBuilder
    )
    {
    }

    public function generate(string $collection = self::COLLECTION_DEFAULT): OpenApi
    {
        $middlewares = Arr::get($this->config, 'collections.' . $collection . '.middlewares');

        $specVersion = Arr::get($this->config, 'collections.' . $collection . '.spec_version', OpenApi::OPENAPI_3_0_2);
        $info = $this->infoBuilder->build(Arr::get($this->config, 'collections.' . $collection . '.info', []));
        $servers = $this->serversBuilder->build(Arr::get($this->config, 'collections.' . $collection . '.servers', []));
        $tags = $this->tagsBuilder->build(Arr::get($this->config, 'collections.' . $collection . '.tags', []));
        $paths = $this->pathsBuilder->build($collection, Arr::get($middlewares, 'paths', []));
        $components = $this->componentsBuilder->build($collection, Arr::get($middlewares, 'components', []));
        $extensions = Arr::get($this->config, 'collections.' . $collection . '.extensions', []);

        $openApi = OpenApi::create()
            ->openapi($specVersion)
            ->info($info)
            ->servers(...$servers)
            ->paths(...$paths)
            ->components($components)
            ->security(...Arr::get($this->config, 'collections.'.$collection.'.security', []))
            ->tags(...$tags);

        foreach ($extensions as $key => $value) {
            $openApi = $openApi->x($key, $value);
        }

        return $openApi;
    }

    public function schemasAreCached(): bool
    {
        return app('files')->exists($this->getCachedSchemasPath());
    }

    /**
     * Get the path to the schemas cache file.
     */
    public function getCachedSchemasPath(): string
    {
        if (is_null($env = Env::get('OPENAPI_SCHEMAS_CACHE'))) {
            return app()->bootstrapPath('cache/openapi-schemas.php');
        }

        return Str::startsWith($env, ['/', '\\'])
            ? $env
            : app()->basePath($env);
    }
}
