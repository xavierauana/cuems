<?php

namespace App\Http\Controllers;

use App\Event;
use App\Exports\PositionGroupingExport;
use App\Exports\PositionGroupingImport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class PositionGroupingController extends Controller
{
    public function index(Event $event) {
        $groupings = $event->positionGroupings()
                           ->orderBy(request()->get('orderBy',
                               'position'),
                               request()->get('order', 'dec'))->paginate();

        return view("admin.events.position-groupings.index",
            compact('event', 'groupings'));
    }

    public function export(Event $event) {
        return Excel::download(new PositionGroupingExport($event),
            'position_groupings.xls');
    }

    public function getImport(Event $event) {
        return view('admin.events.position-groupings.import', compact('event'));
    }

    public function import(Event $event) {
        $this->validate(request(), [
            'file' => 'required|file|min:1'
        ]);

        $file = request()->file('file');

        $collection = Excel::toCollection(new PositionGroupingImport(),
            $file)->first();

        $errors = [];

        $rules = [
            'position' => 'required'
        ];

        $validate = function (Collection $data) use (&$errors, $rules) {
            $validator = Validator::make($data->toArray(), $rules);

            if ($validator->fails()) {
                $errors[] = $validator->getMessageBag()->toArray();

                return false;
            }

            return true;
        };
        $update = function (Collection $data) use ($event) {
            $data = $data->toArray();

            try {
                $groupings = $event->positionGroupings()
                                   ->wherePosition($data['position'])
                                   ->firstOrFail();
            } catch (\Exception $e) {
                dd($data, $data['position']);
            }


            $groupings->update([
                'position' => $data['position'],
                'grouping' => $data['grouping'],
            ]);
        };
        $collection->filter($validate)->each($update);

        return redirect()->route("events.position-groupings.index")
                         ->withStatus('Position groupings is updated! ' . count($errors) . " errors.");

    }
}
