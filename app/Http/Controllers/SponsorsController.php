<?php

namespace App\Http\Controllers;

use App\Event;
use App\Imports\SponsorsImport;
use App\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SponsorsController extends Controller
{
    /**
     * @var Sponsor
     */
    private $repo;

    /**
     * SponsorsController constructor.
     * @param \App\Sponsor $repo
     */
    public function __construct(Sponsor $repo) {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Event $event
     * @return void
     */
    public function index(Event $event) {

        $event->load([
            'sponsors' => function ($q) {
                return $q->orderBy('name');
            }
        ]);

        return view('admin.events.sponsors.index', compact('event'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Event $event) {
        return view('admin.events.sponsors.create', compact('event'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, Event $event) {
        $validatedData = $this->validate($request,
            $this->repo->getStoreRules($event->id),
            $this->repo->getValidationMessage($event->id));

        $event->sponsors()->create($validatedData);

        return redirect()->route('events.sponsors.index', $event)
                         ->withStatus("New sponsor created!");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sponsor $sponsor
     * @return \Illuminate\Http\Response
     */
    public function show(Sponsor $sponsor) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Event    $event
     * @param  \App\Sponsor $sponsor
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event, Sponsor $sponsor) {
        return view("admin.events.sponsors.edit", compact('sponsor', 'event'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Event                $event
     * @param  \App\Sponsor             $sponsor
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Event $event, Sponsor $sponsor) {
        $validatedData = $this->validate($request,
            $sponsor->getUpdateRules($event->id),
            $sponsor->getValidationMessage());

        $sponsor->update($validatedData);

        return redirect()->route("events.sponsors.index", $event)
                         ->withStatus("Sponsor: {$sponsor->name} is updated!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sponsor $sponsor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event, Sponsor $sponsor) {
        $event->sponsors()->whereId($sponsor->id)->delete();

        return redirect()->back()
                         ->withStatus("Sponsor {$sponsor->name} is deleted!");

    }

    public function getImport(Event $event) {
        return view('admin.events.sponsors.import', compact('event'));
    }

    /**
     * @param \App\Event $event
     * @param Request    $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postImport(Event $event, Request $request) {

        $this->validate($request, [
            'file' => 'required|file|min:0.1'
        ]);

        $raw = Excel::toCollection(new SponsorsImport,
            $request->file('file'));

        $collection = $raw->first()->filter(function ($row) {
            return Validator::make($row->toArray(), [
                'name' => 'required'
            ])->passes();
        });
        $collection->each(function ($row) use ($event) {
            $event->sponsors()->create($row->toArray());
        });

        $numberError = $raw->first()->count() - $collection->count();

        return redirect()->route('events.sponsors.index', $event)
                         ->withStatus("{$collection->count()} sponsors are created! {$numberError} error.");
    }

    public function template() {
        return response()->download(storage_path('app/templates/sponsors_template.xlsx'));
    }
}
