<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Nwidart\Modules\Facades\Module;

class ProjectModules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:modules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable modules';

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
        foreach (Module::all() as $item) {
            Artisan::call('module:enable ' . $item->getName());
            $this->info($item->getName() . " has been enabled!");
        }
    }
}
