<?php

namespace App\Console\Commands;

date_default_timezone_set('Asia/Taipei');

use \Curl\Curl;
use DB;
use App\Jobs\insertqueueJob;
use Illuminate\Console\Command;

class InsertQueueTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:queueTime';

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
        $date = date("Y-m-d");
        $time = date("H:i", mktime(date('H'), date('i') - 1));

        $curl = new Curl();
        $curl->get("http://train.rd6/?start={$date}T{$time}:00&end={$date}T{$time}:59&from=0");

        if ($curl->error) {
            echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
        } else {
            echo 'Response:' . "\n";
            // var_dump($curl->response);
        }

        $data = json_decode($curl->response, true);

        foreach ($data['hits']['hits'] as $k => $v) {
            // $data['hits']['hits'][$k]['_source'] = json_encode($data['hits']['hits'][$k]['_source']);
            // $data['hits']['hits'][$k]['sort'] = json_encode($data['hits']['hits'][$k]['sort']);
            $source = "";
            $sort = "";
            foreach ($v['_source'] as $sourceKey => $sourceValue) {
                $source .= "{$sourceKey} : {$sourceValue},  ";
            }
            foreach ($v['sort'] as $sortKey => $sortValue) {
                $sort .= "{$sortKey} : {$sortValue},  ";
            }
            $data['hits']['hits'][$k]['_source'] = $source;
            $data['hits']['hits'][$k]['sort'] = $sort;
        };

        $dataChunks = array_chunk($data['hits']['hits'], 1000, true);
        $count = 0;
        foreach ($dataChunks as $value) {
            $count += count($value);
            insertqueueJob::dispatch($value);
            echo $count . "\n";
        }

        $curl->close();
    }
}
