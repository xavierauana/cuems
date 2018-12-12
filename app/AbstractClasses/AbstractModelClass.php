<?php

namespace App\AbstractClasses;


use Illuminate\Database\Eloquent\Model;

abstract class AbstractModelClass extends Model
{
    protected $appends = [
        'actions'
    ];

    abstract public function getStoreRules(array $params = []): array;

    public function getUpdateRules(array $params = []): array {
        return $this->getStoreRules($params);
    }

    public function getActionsAttribute(): array {
        return [
            'edit'   => route($this->getTable() . '.edit', $this),
            'delete' => route($this->getTable() . '.destroy', $this),
        ];
    }
}