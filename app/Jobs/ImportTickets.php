<?php

namespace App\Jobs;

use App\Event;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

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
     * @param \App\Ticket $ticket
     * @return void
     */
    public function handle(Ticket $ticket) {

        foreach ($this->getData() as $record) {
            $sanitizedRecord = $this->sanitizeData($record);
            if ($validatedData = $this->validate($ticket, $sanitizedRecord)) {
                $this->event->tickets()->create($validatedData);
            }
        }
    }

    private function getData() {

        $handle = fopen($this->filePath, "r");

        $header = null;
        while ($data = fgetcsv($handle)) {

            if ($header === null) {
                $header = array_map('trim', $data);
            } else {
                yield array_combine($header, array_map('trim', $data));
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
        $record['start_at'] = Carbon::createFromFormat("d/m/Y H:i",
            $record['start_at']);
        $record['end_at'] = Carbon::createFromFormat("d/m/Y H:i",
            $record['end_at']);
        $record['price'] = floatval($record['price']);
        $record['vacancy'] = empty($record['vacancy']) ? null : $record['vacancy'];
        $record['is_public'] = (!empty($record['is_public']) and strtolower($record['is_public']) == "false") ? false : true;

        return $record;
    }

    private function validate(Ticket $ticket, $record): ?array {
        $rules = $ticket->getStoreRules($this->event->id);
        $validator = Validator::make($record, $rules);

        if ($validator->passes()) {
            return $validator->validate();
        }

        return null;

    }
}
