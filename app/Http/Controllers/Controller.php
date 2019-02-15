<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function constructSearchQuery(
        Model $repo, Request $request, $query, $keyword = 'keyword'
    ) {
        $columns = $repo->getSearchableColumns();
        if ($searchKeyword = $request->query($keyword)) {
            $query->where(function ($q) use ($columns, $searchKeyword
            ) {
                foreach ($columns as $index => $column) {
                    if ($index === 0) {
                        $q->where($column, "like",
                            "%{$searchKeyword}%");
                    } else {
                        $q->orWhere($column, "like",
                            "%{$searchKeyword}%");
                    }
                }
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