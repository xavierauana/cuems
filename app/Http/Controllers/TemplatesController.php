<?php

namespace App\Http\Controllers;

use App\Event;
use App\Template;
use Illuminate\Http\Request;

class TemplatesController extends Controller
{
    /**
     * @var \App\Template
     */
    private $repo;

    /**
     * TemplatesController constructor.
     * @param \App\Template $repo
     */
    public function __construct(Template $repo) {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Event $event
     * @return void
     */
    public function index(Event $event) {

        $templates = $event->templates()->paginate(100);

        return view("admin.templates.index", compact("event","templates"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Event $event
     * @return void
     */
    public function create(Event $event) {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Event                $event
     * @return void
     */
    public function store(Request $request, Event $event) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Event     $event
     * @param  \App\Template $template
     * @return void
     */
    public function show(Event $event, Template $template) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Event     $event
     * @param  \App\Template $template
     * @return void
     */
    public function edit(Event $event, Template $template) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Event                $event
     * @param  \App\Template            $template
     * @return void
     */
    public function update(Request $request, Event $event, Template $template) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Event     $event
     * @param  \App\Template $template
     * @return void
     */
    public function destroy(Event $event, Template $template) {
        //
    }
}
