<?php

namespace App\Console\Commands;

use App\Http\Controllers\ReportEnviarEmailConErrorController;
use Illuminate\Console\Command;

class EnviarEmailConError extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erroremail:enviaremailconerror';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia correo con el listado empleados con error en el correo';

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
        $controller = new ReportEnviarEmailConErrorController();
        $controller->sendemail();
    }
}
