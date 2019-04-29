<?php
/**
 * Author: Xavier Au
 * Date: 2019-03-23
 * Time: 14:15
 */

namespace App\Contracts;


use Illuminate\Database\Eloquent\Builder;

interface SearchableModel
{
    public function scopeSearch(Builder $q, string $keyword = null);
}