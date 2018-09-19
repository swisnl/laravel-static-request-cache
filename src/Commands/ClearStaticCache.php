<?php

namespace Swis\LaravelStaticRequestCache\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ClearStaticCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'static-html-cache:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear static html-response caches';

    /**
     * @var Filesystem
     */
    private $files;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;

        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->clearStorageDirectory(config('static-html-cache.cache_path_prefix'));

        $this->info('Static caches cleared!');
    }

    /**
     * @param string $path
     */
    private function clearStorageDirectory(string $path)
    {
        $path = public_path($path);
        $this->comment("Clearing `{$path}`…");

        if ($this->files->isDirectory($path)) {
            $this->files->deleteDirectory($path, true);
        }
    }
}
