<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Cog\Contracts\Ban\Bannable as BannableContract;
use Cog\Laravel\Ban\Traits\Bannable;




class User extends Authenticatable implements BannableContract, MustVerifyEmail {
    use Bannable;
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'national_id',
        'avatar_url',
        'manageable_id',
        'manageable_type',
        'gender',
        'date_of_birth',
        'last_login',
        'banned_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pivot',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date:d/m/Y',
    ];

    public function manageable() {
        return $this->morphTo();
    }

    public function sessions() {
        return $this->belongsToMany(Session::class);
    }

    public function buys() {
        return $this->morphMany(Purchase::class, 'buyable');
    }

    public function sells() {
        return $this->morphMany(Purchase::class, 'sellable');
    }

    public function gyms() {
        return $this->morphMany(Gym::class, 'creatable');
    }

    public function routeNotificationForMail($notification) {
        // Return email address only...
        return $this->email;

        // Return email address and name...
        return [$this->email => $this->name];
    }
}
