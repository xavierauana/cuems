<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function constructSearchQuery(
        Model $repo, $query, string $keyword = null, Event $event
    ) {
        if ($keyword) {
            $query->where(function (Builder $q) use ($repo, $keyword, $event
            ) {
                $prefix = setting($event, 'registration_id_prefix');
                $sql = "CONCAT('" . $prefix . "',lpad(CAST(registration_id AS CHAR), 4, '0'))  like '%" . $keyword . "%'";

                foreach ($repo->getSearchableColumns() as $index => $column) {
                    if ($index === 0) {
                        $q->where($column, "like",
                            "%{$keyword}%");
                    } else {
                        $q->orWhere($column, "like",
                            "%{$keyword}%");
                    }
                }


                $q->orWhereRaw($sql);

            });
        }

        return $query;
    }

    protected function orderByQuery(array $queries, $query) {
        foreach ($queries as $key => $oder) {
            if ($key !== "page") {
                $query = $query->orderBy($key, $oder);
            }
        }

        return $query;
    }

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
