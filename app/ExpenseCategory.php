<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'name'
    ];


    protected $appends = [
        'urls',
    ];

    // Relation
    public function expenses(): Relation {
        return $this->hasMany(Expense::class);
    }

    // Accessor
    public function getUrlsAttribute() {
        return [
            'edit'   => route('expense_categories.edit', $this),
            'delete' => route('expense_categories.destroy', $this),
        ];
    }
}
