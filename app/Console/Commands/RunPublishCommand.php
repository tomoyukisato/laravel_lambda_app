<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InvoiceService;


class RunPublishCommand extends Command
{
    private $invoiceService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:publish_invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MoneyForwardで請求書を発行します';

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

        return $this->invoiceService->publish();
    }
}
