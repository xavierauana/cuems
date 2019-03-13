<?php

namespace App;

use App\Enums\DelegateDuplicationStatus;
use Collective\Html\Eloquent\FormAccessible;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Delegate extends Model
{
    use Notifiable, SoftDeletes, FormAccessible;

    protected $searchableColumns = [
        'registration_id',
        'last_name',
        'first_name',
        'mobile',
        'email',
        'institution',
    ];

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
        'country',
        'training_organisation',
        'training_organisation_address',
        'supervisor',
        'training_position',
        'address_1',
        'address_2',
        'address_3',
        'is_duplicated',
        'is_verified',
        'registration_id',
        'duplicated_with'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function searchableAs() {
        return 'posts_index';
    }

    public function toSearchableArray() {
        $array = $this->toArray();

        return $array;
    }

    // Relation
    public function event(): Relation {
        return $this->belongsTo(Event::class);
    }

    public function roles(): Relation {
        return $this->belongsToMany(DelegateRole::class)
                    ->withPivot('delegate_role_id');
    }

    public function sponsorRecord(): Relation {
        return $this->hasOne(SponsorRecord::class);

    }

    public function transactions(): Relation {
        return $this->morphMany(Transaction::class, "payee");
    }

    // Scope
    public function scopeExcludeRole($query, $role): Builder {
        $roleCode = null;
        if ($role instanceof DelegateRole) {
            $roleCode = $role->code;
        } elseif (is_string($role)) {
            $roleCode = $role;
        }

        return $query->whereIn('id', function ($query) use ($roleCode) {
            $query->select('delegate_delegate_role.delegate_id')
                  ->from('delegate_delegate_role')
                  ->join("delegate_roles", 'delegate_roles.id', '=',
                      'delegate_delegate_role.delegate_role_id')
                  ->where('delegate_roles.code', '<>', $roleCode)
                  ->distinct();
        });
    }

    public function scopeHasRole($query, $role): Builder {
        $roleCode = null;
        if ($role instanceof DelegateRole) {
            $roleCode = $role->code;
        } elseif (is_string($role)) {
            $roleCode = $role;
        }

        return $query->join('delegate_delegate_role',
            'delegate_delegate_role.delegate_id', '=', 'delegates.id')
                     ->whereIn("delegate_delegate_role.delegate_role_id",
                         function ($q) use ($roleCode) {
                             $q->select('id')
                               ->from('delegate_roles')
                               ->where('code', '=', $roleCode);
                         });
    }

    public function scopeTickets($query): Builder {
        return $query->select('delegates.*')
                     ->join('transactions', 'payee_id', '=', 'delegates.id')
                     ->where('payee_type', Delegate::class)
                     ->join('tickets', 'transactions.ticket_id', "=",
                         "tickets.id");
    }

    /**
     * Delegate whose ticket is sponsored
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSponsored($query): Builder {
        return $query->tickets()->where('tickets.note', 'like', '%sponsored%');
    }

    /**
     * Delegate whose ticket is wavied
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWaived($query): Builder {
        return $query->tickets()->where('tickets.note', 'like', '%waived%');
    }

    // Accessor
    public function getNameAttribute(): string {
        return $this->prefix . " " . $this->first_name . " " . $this->last_name;
    }

    public function getTicketIdAttribute(): int {
        return $this->transactions()->with('ticket')->latest()
                    ->first()->ticket->id;
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

    public function formTransactionTypeIdAttribute(): ?int {
        return optional($this->transactions()->first())->transaction_type_id;
    }

    // Helper

    public function getStoreRules(): array {

        return [
            'prefix'                        => "required",
            'first_name'                    => "required",
            'last_name'                     => "required",
            'is_male'                       => "required|boolean",
            'position'                      => "required",
            'other_position'                => "nullable|required_if:position,Others",
            'department'                    => "required",
            'institution'                   => "required",
            'other_institution'             => "nullable|required_if:institution,Others",
            'address_1'                     => "required",
            'address_2'                     => "nullable",
            'address_3'                     => "nullable",
            'email'                         => "required|email",
            'mobile'                        => "required",
            'fax'                           => 'nullable',
            'country'                       => 'required',
            'training_organisation'         => 'nullable|traineeInfoRequired',
            'training_other_organisation'   => 'nullable|required_if:training_organisation,Others',
            'training_organisation_address' => 'nullable|traineeInfoRequired',
            'supervisor'                    => 'nullable|traineeInfoRequired',
            'training_position'             => 'nullable|traineeInfoRequired',
            'is_duplicated'                 => 'nullable|in:' . implode(",",
                    array_values(DelegateDuplicationStatus::getStatus())),
            'is_verified'                   => 'nullable|boolean',
            'roles_id'                      => 'nullable',
            'roles_id.*'                    => 'nullable|exists:delegate_roles,id',
            'duplicated_with'               => 'nullable',

            'sponsor'            => 'nullable',
            'sponsor.email'      => 'nullable|email',
            'sponsor.name'       => 'nullable',
            'sponsor.address'    => 'nullable',
            'sponsor.sponsor_id' => 'nullable|exists:sponsors,id',
        ];
    }

    public function routeNotificationForMail(): string {
        return $this->email;
    }

    public function markDuplicated(): void {
        $this->is_duplicated = DelegateDuplicationStatus::DUPLICATED;
        $this->save();
    }

    public function isDuplicated(): bool {
        return $this->is_duplicated === DelegateDuplicationStatus::DUPLICATED;
    }

    public function getRegistrationId(): string {

        $prefix = setting($this->event, 'registration_id_prefix') ?? "";

        return $prefix . str_pad($this->registration_id, 4, 0,
                STR_PAD_LEFT);
    }

    /**
     * @return array
     */
    public function getSearchableColumns(): array {
        return $this->searchableColumns;
    }


}
