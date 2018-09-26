<?php

namespace App\Http\Controllers;

use App\Event;

class DashboardController extends Controller
{
    public function index() {

        $activeEvents = Event::all();

        return view("home", compact('activeEvents'));
    }
}
