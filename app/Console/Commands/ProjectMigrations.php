<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Nwidart\Modules\Facades\Module;

class ProjectMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:migrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs all migrations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $modules = [];

        foreach (Module::getCached() as $module) {
            $modules[$module['order']] = $module['name'];
        }

        ksort($modules);
        foreach ($modules as $module) {
            Artisan::call("module:migrate $module");
            $this->info("$module migrate finish!");
        }

        Artisan::call('storage:link');
        $this->info('link finish!');
    }
}
