<?php

namespace App;

use Adldap\Laravel\Traits\HasLdapUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasLdapUser, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_ldap_user' => 'boolean'
    ];

    protected $appends = [
        'urls',
    ];

    public function getUrlsAttribute() {
        return [
            'edit'    => route('users.edit', $this),
            'delete'  => route('users.destroy', $this),
            'restore' => $this->trashed() ?
                route('users.restore',
                    $this) : null,
        ];
    }

    // helpers

    public function isLdapUser(): bool {
        return $this->is_ldap_user;
    }
}
