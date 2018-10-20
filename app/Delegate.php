<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\Notifiable;

class Delegate extends Model
{
    use Notifiable;

    protected $fillable = [
        'prefix',
        'first_name',
        'last_name',
        'is_male',
        'position',
        'department',
        'institution',
        'address',
        'email',
        'mobile',
        'fax',
        'training_organisation',
        'training_organisation_address',
        'supervisor',
        'training_position',
    ];

    // Relation
    public function event(): Relation {
        return $this->belongsTo(Event::class);
    }

    public function roles(): Relation {
        return $this->belongsToMany(DelegateRole::class);
    }

    public function transactions(): Relation {
        return $this->morphMany(Transaction::class, "payee");
    }

    // Accessor
    public function getNameAttribute(): string {
        return $this->last_name . " " . $this->first_name;
    }

    public function getTicketIdAttribute(): int {
        return $this->ticket->ticket_id;
    }

    public function getTicketAttribute(): Transaction {
        return $this->transactions()->latest()->first();
    }

    public function getNoteAttribute(): ?string {
        return $this->transactions()->latest()->first()->note;
    }

    public function getStatusAttribute(): string {
        return $this->transactions()->latest()->first()->status;
    }

    public function getRolesIdAttribute(): array {
        return $this->roles()->pluck('id')->toArray();
    }

    // Helper

    public function getStoreRules(): array {

        return [
            'prefix'                        => "required",
            'first_name'                    => "required",
            'last_name'                     => "required",
            'is_male'                       => "required|boolean",
            'position'                      => "required",
            'department'                    => "required",
            'institution'                   => "required",
            'address'                       => "required",
            'email'                         => "required|email",
            'mobile'                        => "required",
            'fax'                           => 'nullable',
            'training_organisation'         => 'nullable',
            'training_organisation_address' => 'nullable',
            'supervisor'                    => 'nullable',
            'training_position'             => 'nullable',
            'roles_id.*'                    => 'nullable|in:' . implode(",",
                    DelegateRole::pluck('id')->toArray()),
        ];
    }

    public function routeNotificationForMail(): string {
        return $this->email;
    }
}
