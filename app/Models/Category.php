<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name','coloc_id'];

    public function expenses(){
        return $this->hasMany(Expense::class, 'category_id');
    }
    public function colocation(){
        return $this->belongsTo(Colocation::class, 'coloc_id');
    }
}
