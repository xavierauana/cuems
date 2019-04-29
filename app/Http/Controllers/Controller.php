<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function abortIfNotEventSubEntity(Model $model, Event $event
    ): void {
        try {
            if ($model->event->isNot($event)) {
                {
                    abort(403);
                }
            }
        } catch (\Exception $e) {
            abort(403);
        }

    }
}
