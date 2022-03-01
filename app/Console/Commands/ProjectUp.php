<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProjectUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Up migration and default data';

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
        $bar = $this->output->createProgressBar(4);

        $bar->start();
        $this->newLine();

        $this->comment("Start enable modules");
        Artisan::call('project:modules');
        $this->info("Finish enable modules");

        $bar->advance();
        $this->newLine();

        $this->comment("Start migrations");
        Artisan::call('project:migrations');
        $this->info("Finish migrations");

        $bar->advance();
        $this->newLine();

        $this->comment("Start seeds");
        Artisan::call('project:seeds');
        $this->info("Finish seeds");

        $bar->advance();
        $this->newLine();

        Artisan::call('storage:link');
        $this->info('Link created!');

        $bar->advance();
        $this->newLine();

        $bar->finish();
    }
}
