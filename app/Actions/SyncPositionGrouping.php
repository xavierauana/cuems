<?php
/**
 * Author: Xavier Au
 * Date: 2019-07-23
 * Time: 20:35
 */

namespace App\Actions;


use App\Event;
use App\PositionGrouping;

class SyncPositionGrouping
{
    public static function sync(Event $event) {
        $positions = $event->delegates()->distinct('position')
                           ->pluck('position');
        $positions->each(function (String $item) use ($event) {
            if (!PositionGrouping::wherePosition($item)
                                 ->whereEventId($event)->exists()) {
                $event->positionGroupings()->create(['position' => $item]);
            }
        });

        $event->positionGroupings()
              ->select('event_id', 'id', 'position')
              ->distinct('position')
              ->get()
              ->each(function (PositionGrouping $grouping) use ($positions) {
                  if (!$positions->contains($grouping->position)) {
                      $grouping->delete();
                  }
              });

    }
}