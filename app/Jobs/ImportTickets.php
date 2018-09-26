<?php

namespace App\Jobs;

use App\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class ImportTickets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var \App\Event
     */
    private $event;

    /**
     * @var string
     */
    private $filePath;

    /**
     * Create a new job instance.
     *
     * @param \App\Event $event
     * @param string     $filePath
     */
    public function __construct(Event $event, string $filePath) {
        //
        $this->event = $event;
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        foreach ($this->getData() as $record) {

            $record = $this->sanitizeData($record);

            $this->event->tickets()->create($record);
        }
    }

    private function getData() {

        $handle = fopen($this->filePath, "r");

        $header = null;
        while ($data = fgetcsv($handle)) {

            if ($header === null) {
                $header = $data;
            } else {
                yield array_combine($header, $data);
            }

        }

        fclose($handle);

        File::delete($this->filePath);

    }

    /**
     * @param $record
     * @return mixed
     */
    private function sanitizeData($record) {
        $record['start_at'] = new Carbon($record['start_at']);
        $record['end_at'] = new Carbon($record['end_at']);
        $record['vacancy'] = empty($record['vacancy']) ? null : $record['vacancy'];

        return $record;
    }
}
