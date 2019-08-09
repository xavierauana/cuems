<?php

namespace App\Observers;

use App\Actions\SyncPositionGrouping;
use App\Delegate;

class DelegateObserver
{
    public function saved(Delegate $delegate) {
        SyncPositionGrouping::sync($delegate->event);
    }

    public function deleted(Delegate $delegate) {
        SyncPositionGrouping::sync($delegate->event);
    }
}
