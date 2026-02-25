<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Colocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'status',
        'owner_id',
    ];
    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');

    }
    public function memberships(){
        return $this->hasMany(Membership::class, 'coloc_id');
    }
    
    public function invitations(){
        return $this->hasMany(Invitation::class, 'coloc_id');
    }

    public function activeMemberships(){
        return $this->memberships()->whereNull('left_at');
    }


}
