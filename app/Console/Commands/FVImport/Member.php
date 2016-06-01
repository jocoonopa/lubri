<?php

namespace App\Console\Commands\FVImport;

use App\Export\FVImport\MemberExport;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Member extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importmember:fv {startat?} {endat?} {--size=100} {--serno=MEMBR000000000000000000001}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle(MemberExport $export)
    {
        set_time_limit(0);

        $this->info("\r\n\r\n------------- {$export->getFilename()} MemberExportProcess Begin! -------------\r\n\r\n");

        $export
            ->setCommend($this)
            ->setOutput($this->output)
            ->setSize($this->option('size'))
            ->setSerno($this->option('serno'))
            ->setStartAt($this->argument('startat'))
            ->setEndAt($this->argument('endat'))
            ->handleExport()
        ;

        $this->comment("\r\nExport Complete!");
    }
}
