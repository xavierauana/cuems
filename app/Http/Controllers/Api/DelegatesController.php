<?php

namespace App\Http\Controllers\Api;

use App\Delegate;
use App\Event;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiDelegateResource;
use Illuminate\Http\Request;

class DelegatesController extends Controller
{
    public function getDelegates(Request $request, Event $event) {

        if (is_null($request->get('role')) or $request->get('role') === 'default') {
            return ;
        }
        $delegates = Delegate::select('delegates.*')
                             ->join('events as e', 'delegates.event_id', '=',
                                 'e.id')
                             ->join('delegate_delegate_role as p',
                                 'delegates.id', '=', 'p.delegate_id')
                             ->join('delegate_roles as r', 'r.id', '=',
                                 'p.delegate_role_id')
                             ->where('e.id', $event->id)
                             ->where('r.code', $request->get('role'))
                             ->get();

        return ApiDelegateResource::collection($delegates);
    }
}
