<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Nwidart\Modules\Facades\Module;

class ProjectSeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:seeds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all seeds';

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
        foreach (Module::getOrdered() as $moduleName => $module) {
            $this->comment("$moduleName seed start!");
            Artisan::call("module:seed $moduleName");
            $this->info("$moduleName seed finish!");
        }
    }
}
