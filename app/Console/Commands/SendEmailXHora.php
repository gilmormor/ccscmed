<?php

namespace App\Console\Commands;

use App\Http\Controllers\ReportRecHonSendEmailController;
use Illuminate\Console\Command;

class SendEmailXHora extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:sendxhora';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía correos cada hora';

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
        $controller = new ReportRecHonSendEmailController();
        $controller->sendemailxhora();
    }
}
