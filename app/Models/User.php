<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'reputation',
        'is_banned',
        'banned_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'reputation'=> 'integer',
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
        ];
    }
    public function ownedColocations(){
        return $this->hasMany(Colocation::class, 'owner_id');
    }
    public function memberships(){
        return $this->hasMany(Membership::class);
    }
    
    public function paidExpenses(){
        return $this->hasMany(Expense::class, 'payer_id');
    }

    public function hasActiveColocation()
    {
        return $this->memberships()
                    ->whereNull('left_at')
                    ->whereHas('colocation', function ($query) {
                        $query->where('status', 'active');
                    })
                    ->exists();
    }
}
