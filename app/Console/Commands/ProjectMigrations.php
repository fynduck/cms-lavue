<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

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
        Artisan::call('module:migrate Language');
        $this->info("Language migrate finish!");

        Artisan::call('module:migrate UserGroup');
        $this->info("UserGroup migrate finish!");

        Artisan::call('module:migrate User');
        $this->info("User migrate finish!");

        Artisan::call('module:migrate Settings');
        $this->info("Settings migrate finish!");

        Artisan::call('module:migrate Menu');
        $this->info("Menu migrate finish!");

        Artisan::call('module:migrate Page');
        $this->info("Page migrate finish!");

        Artisan::call('module:migrate Article');
        $this->info("Article migrate finish!");

        Artisan::call('module:migrate CustomForm');
        $this->info("CustomForm migrate finish!");

        Artisan::call('module:migrate Redirect');
        $this->info("Redirect migrate finish!");

        Artisan::call('storage:link');
        $this->info('link finish!');
    }
}
