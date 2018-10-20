<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rule;

class DelegateRole extends Model
{

    protected $fillable = [
        'code',
        'label',
        'is_default'
    ];

    protected $appends = [
        "urls",
        'is_default_formatted'
    ];

    protected $casts = [
        'is_default' => "boolean"
    ];

    // Relation
    public function delegates(): Relation {
        return $this->belongsToMany(DelegateRole::class);
    }

    // Mutator
    public function setIsDefaultAttribute($value): void {
        if ($value) {
            $this->whereIsDefault(true)->update([
                'is_default' => false
            ]);
        }

        $this->attributes['is_default'] = $value;
    }

    // Accessor
    public function getUrlsAttribute(): array {
        return [
            'edit'   => route("roles.edit", $this),
            'delete' => route("roles.destroy", $this),
        ];
    }

    public function getIsDefaultFormattedAttribute(): string {
        return $this->is_default ? "Yes" : "No";
    }

    // Helpers
    public function getStoreRules(): array {
        return [
            'label'      => 'required',
            'is_default' => 'nullable|boolean',
            'code'       => 'required|unique:delegate_roles',
        ];
    }

    public function getUpdateRules(): array {
        return [
            'label'      => 'required',
            'is_default' => 'nullable|boolean',
            'code'       => [
                'required',
                Rule::unique('delegate_roles')->ignore($this->id)
            ],
        ];
    }

}
