<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Membership extends Model
{
    use HasFactory;
    protected $fillable = [
        'role',
        'joined_at',
        'left_at',
        'user_id',
        'coloc_id',

    ];
    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
        ];
    }
    
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coloc(){
        return $this->belongsTo(Colocation::class, 'coloc_id');
    }
}
