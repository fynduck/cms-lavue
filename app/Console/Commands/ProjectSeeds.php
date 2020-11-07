<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

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
        $this->comment("Language seed start!");
        Artisan::call('module:seed Language');
        $this->info("Language seed finish!");

        $this->comment("Page seed start!");
        Artisan::call('module:seed Page');
        $this->info("Page seed finish!");

        $this->comment("UserGroup seed start!");
        Artisan::call('module:seed UserGroup');
        $this->info("UserGroup seed finish!");

        $this->comment("User seed start!");
        Artisan::call('module:seed User');
        $this->info("User seed finish!");

        $this->comment("Settings seed start!");
        Artisan::call('module:seed Settings');
        $this->info("Settings seed finish!");
    }
}
