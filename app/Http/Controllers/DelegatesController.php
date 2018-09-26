<?php

namespace App\Http\Controllers;

use App\Delegate;
use App\Event;
use Illuminate\Http\Request;

class DelegatesController extends Controller
{
    /**
     * @var \App\Delegate
     */
    private $repo;

    /**
     * DelegatesController constructor.
     */
    public function __construct(Delegate $delegate) {
        $this->repo = $delegate;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Event $event) {
        $delegates = $event->delegates;

        return view('admin.events.delegates.index',
            compact('event', 'delegates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Delegate $delegate
     * @return \Illuminate\Http\Response
     */
    public function show(Delegate $delegate) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Delegate $delegate
     * @return \Illuminate\Http\Response
     */
    public function edit(Delegate $delegate) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Delegate            $delegate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Delegate $delegate) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Delegate $delegate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Delegate $delegate) {
        //
    }
}
