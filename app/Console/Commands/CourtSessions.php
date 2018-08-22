<?php

namespace Toecyd\Console\Commands;

use Illuminate\Console\Command;

class CourtSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:court_sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отримати список всіх судових засідаль всіх судів';

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
        echo "It works\n";
        //
    }
}
