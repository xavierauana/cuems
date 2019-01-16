<?php

namespace App\Http\Controllers;

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
            foreach ($columns as $index => $column) {
                if ($index === 0) {
                    $query = $query->where($column, "like",
                        "%{$searchKeyword}%");
                } else {
                    $query = $query->orWhere($column, "like",
                        "%{$searchKeyword}%");
                }
            }
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
}
