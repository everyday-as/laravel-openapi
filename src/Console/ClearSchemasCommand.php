<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Console;

use Illuminate\Console\Command;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Vyuldashev\LaravelOpenApi\Generator;

class ClearSchemasCommand extends Command
{
    protected $signature = 'openapi:clear-schemas';
    protected $description = 'Clear cached OpenAPI schema(s)';

    public function __construct(protected Filesystem $files, protected Generator $generator)
    {
        parent::__construct();
    }

    /**
     * @throws FilesystemException
     */
    public function handle(): void
    {
        $this->files->delete($this->generator->getCachedSchemasPath());

        $this->info('Schema cache cleared successfully.');
    }
}
