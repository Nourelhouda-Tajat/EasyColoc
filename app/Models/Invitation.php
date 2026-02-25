<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Invitation extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'email',
        'token',
        'status',
        'coloc_id',
    ];

    public function colocation(){
        return $this->belongsTo(Colocation::class, 'coloc_id');
    }
}
