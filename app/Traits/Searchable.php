<?php
/**
 * Author: Xavier Au
 * Date: 2019-03-23
 * Time: 14:17
 */

namespace App\Traits;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Trait Searchable
 * @property array $searchableColumns
 * @package App\Traits
 */
trait Searchable
{

    public function scopeSearch(
        Builder $q, string $keyword = null, array $args = []
    ) {
        if (is_null($keyword)) {
            return $q;
        }
        $searchableColumns = $this->searchableColumns ?? [];


        foreach ($searchableColumns as $index => $column) {
            $checkKeyword = $this->mutateKeyword($keyword, $args, $column);

            if ($index == 0) {
                $q->where($column, 'like', "%{$checkKeyword}%");
            } else {
                $q->orWhere($column, 'like', "%{$checkKeyword}%");
            }
        }
    }

    /**
     * @param string $keyword
     * @param array  $args
     * @param        $column
     * @return string
     */
    private function mutateKeyword(string $keyword, array $args, $column
    ): string {
        $checkKeyword = $keyword;
        $str = Str::studly(str_replace(".", "_", $column));
        $method = "keyword{$str}Mutator";
        if (method_exists($this, $method)) {
            $checkKeyword = $this->{$method}($keyword, ...$args);
        }

        return $checkKeyword;
    }
}