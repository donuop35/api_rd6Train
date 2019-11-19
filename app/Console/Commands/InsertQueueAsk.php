<?php

namespace App\Console\Commands;

date_default_timezone_set('Asia/Taipei');

use \Curl\Curl;
use DB;
use App\Jobs\insertqueueJob;
use Illuminate\Console\Command;

class InsertQueueAsk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:queueAsk';

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
    public function handle()
    {
        $num = 0;

        $date = $this->ask('請輸入查詢日期 (例：YYYY-MM-DD)');
        if (!preg_match("/^20[0-9]{2}\-[0-9]{2}-[0-9]{2}$/", $date)) {
            echo "日期輸入錯誤,請重新輸入 \n";
            $date = $this->ask('請輸入查詢日期');
        }

        $time = $this->ask('請輸入查詢時間 (例：00:00)');
        if (!preg_match("/^[0-9]{2}\:[0-9]{2}$/", $time)) {
            echo "時間輸入錯誤,請重新輸入 \n";
            $time = $this->ask('請輸入查詢時間');
        }

        $start_num = $this->ask('請輸入查詢初始筆數 (最小值0)');
        if (!preg_match("/^0|[1-9]0000$/", $start_num)) {
            echo "資料格式錯誤,請重新輸入 \n";
            $start_num = $this->ask('請輸入查詢初始筆數 (最小值0)');
        }

        $end_num = $this->ask('請輸入查詢最終筆數 (最大值100000)');
        if (!preg_match("/^0|[1-9]0000$/", $end_num)) {
            echo "資料格式錯誤,請重新輸入 \n";
            $end_num = $this->ask('請輸入查詢最終筆數 (最大值100000)');
        }

        for ($num = $start_num; $num < $end_num; $num += 10000) {
            echo $num . "\n";
            insertqueueJob::dispatch("http://train.rd6/?start={$date}T{$time}:00&end={$date}T{$time}:59&from={$num}");
        };

        // insertqueueJob::dispatch("http://train.rd6/?start=2019-11-13T10:00:00&end=2019-11-13T10:00:59&from=10000");
    }
}
