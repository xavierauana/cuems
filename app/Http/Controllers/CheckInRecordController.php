<?php

namespace App\Http\Controllers;

use App\Event;
use App\Exports\CheckinRecords;
use App\Http\Requests\NotificationStoreRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CheckInRecordController extends Controller
{
    private $selectedFields = [
        'tickets.name as ticket_name',
        'check_in.id',
        'check_in.created_at',
        'check_in.transaction_id',
        'delegates.id as delegate_id',
        'delegates.first_name',
        'delegates.last_name',
        'delegates.email',
        'delegates.institution',
        'delegates.department',
        'delegates.position',
        'delegates.registration_id',
    ];

    public function index(Event $event, Request $request) {
        $records = $event->getCheckinControllerQuery(
            $request->query('keyword'),
            $request->query('date'))
                         ->select($this->selectedFields)
                         ->paginate();

        $stats = $event->getCheckinJoinQuery()
                       ->select([
                           'transactions.id',
                           'check_in.created_at as check_in_date'
                       ])
                       ->get()
                       ->map(function ($record) {
                           $record->check_in_date = new \Carbon\Carbon($record->check_in_date);

                           return $record;
                       })
                       ->groupBy(function ($record) {
                           return $record->check_in_date->toDateString();
                       })
                       ->map(function ($dateCollection) {
                           return $dateCollection->count();
                       });

        $files = $event->uploadFiles()->pluck('name', 'id');


        return view("admin.events.checkinRecords.index",
            compact('event', 'records', 'stats', 'files'));
    }

    public function export(Event $event, Request $request) {

        return Excel::download(new CheckinRecords($event,
            $event->getCheckinControllerQuery(
                $request->query('keyword'),
                $request->query('date')
            )
                  ->select($this->selectedFields)
        ), $event->getExportCheckinFilename());
    }

    public function notification(NotificationStoreRequest $request, Event $event
    ) {
        $data = $request->validated();

        $event->notifications()->create($data);

        if ($request->ajax()) {
            return response()->json(['status' => 'completed']);
        }

        return redirect()->back()->with('status', 'Notification created!');
    }
}
