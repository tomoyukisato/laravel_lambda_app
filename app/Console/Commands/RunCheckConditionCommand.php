<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InvoiceService;


class RunCheckCommand extends Command
{
    private $invoiceService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:check_invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MoneyForwardで入金チェックを行います';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->invoiceService = new InvoiceService();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        return $this->invoiceService->check();
    }
}
