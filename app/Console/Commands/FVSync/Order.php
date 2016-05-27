<?php

namespace App\Console\Commands\FVSync;

use App\Export\FVSync\OrderExport;
use Illuminate\Console\Command;

class Order extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncorder:fv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Flap Data and trans to Viga';

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
    public function handle(OrderExport $export)
    {
        $bar = $this->output->createProgressBar(50000);
        $bar->setRedrawFrequency(1);
        $bar->setFormat('verbose');
        $bar->setOverwrite(true);

        $i = 0;
        while ($i++ < 50000) {
            $bar->advance();            
        }
        $bar->finish();
        $this->comment("\r\n{$export->getFilename()}");
    }
}
