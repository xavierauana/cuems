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
        if ($keyword = $request->query($keyword)) {
            foreach ($columns as $index => $column) {
                $constraint = $index === 0 ? "where" : "orWhere";
                $query = $query->$constraint($column, "like",
                    "%{$request->get($keyword)}%");
            }
        }

        return $query;
    }
}
