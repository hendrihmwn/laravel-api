<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Voucher;

class VoucherScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voucher:scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is for voucher scheduling';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info("Starting voucher activation..");
        $this->schedule_activation();
        \Log::info("End voucher activation");
        \Log::info("Starting voucher expiration..");
        $this->schedule_expired();
        \Log::info("End voucher expiration");
    }

    protected function schedule_activation(){
        Voucher::whereDate('active_at','<=',date('Y-m-d H:i:s'))
                ->whereDate('expired_at','>=',date('Y-m-d H:i:s'))
                ->where('status','IN_SCHEDULE')
                ->update(['status' => 'ACTIVE']);
    }

    protected function schedule_expired(){
        Voucher::whereDate('expired_at','<=',date('Y-m-d H:i:s'))
                ->where('status','ACTIVE')
                ->update(['status' => 'EXPIRED']);
    }
}
