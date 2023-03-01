<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Throwable;
use Vyuldashev\LaravelOpenApi\Contracts\Generator;

class CacheSchemasCommand extends Command
{
    protected $signature = 'openapi:cache-schemas';
    protected $description = 'Cache OpenAPI schema(s)';

    public function __construct(protected Filesystem $files, protected Generator $generator)
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->call('openapi:clear-schemas');

        $schemas = $this->getSchemas();

        $schemasPath = $this->generator->getCachedSchemasPath();

        $this->files->put(
            $schemasPath,
            '<?php return ' . var_export($schemas, true) . ';'
        );

        try {
            require $schemasPath;
        } catch (Throwable $e) {
            $this->files->delete($schemasPath);

            throw new Exception('Failed to cache OpenAPI schema(s).', 0, $e);
        }

        $this->info('Schema(s) cached successfully.');
    }

    protected function getSchemas(): array
    {
        return collect(config('openapi.collections'))
            ->map(fn($_, $collection) => $this->generator->generate($collection))
            ->all();
    }
}
