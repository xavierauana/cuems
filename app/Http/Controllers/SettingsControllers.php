<?php

namespace App\Http\Controllers;

use App\Setting;
use App\Event;
use Illuminate\Http\Request;

class SettingsControllers extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function index(Event $event)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function create(Event $event)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Event $event)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Event  $event
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event, Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Event  $event
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event, Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Event  $event
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event, Setting $setting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event  $event
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event, Setting $setting)
    {
        //
    }
}
