<?php

namespace App\Http\Controllers;

use App\Event;
use App\PaymentRecord;
use Illuminate\Http\Request;

class PaymentRecordsController extends Controller
{
    /**
     * @var \App\PaymentRecord
     */
    private $repo;

    /**
     * PaymentRecordsController constructor.
     * @param \App\PaymentRecord $repo
     */
    public function __construct(PaymentRecord $repo) {
        $this->repo = $repo;
    }

    public function index(Event $event, Request $request) {
        $query = $this->repo->whereEventId($event->id)
                            ->with('conversion')
                            ->failed()
                            ->latest();
        if ($keyword = $request->query('keyword')) {
            $query->where('invoice_id', 'like', '%' . $keyword . '%');
        }
        $records = $query->paginate();

        return view("admin.events.paymentRecords.index",
            compact('records', 'event'));
    }

    public function show(Event $event, PaymentRecord $record) {
        $record = $event->paymentRecords()->findOrFail($record->id);

        return view("admin.events.paymentRecords.show",
            compact('event', 'record'));

    }

    public function convert(Event $event, PaymentRecord $record) {

        $data = array_merge(json_decode($record->form_data, true),
            ['note' => "Convert form failed payment " . $record->invoice_id]);

        return redirect()->route('events.delegates.create',
            [$event, 'conversion' => $record->id])
                         ->withInput($data);
    }
}
