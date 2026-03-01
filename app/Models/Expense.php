<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'coloc_id',
        'category_id',
        'payer_id',
        'title',
        'amount',
        'date',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
        ];
    }

    public function colocation(){
        return $this->belongsTo(Colocation::class, 'coloc_id');
    }

    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function payer(){
        return $this->belongsTo(User::class, 'payer_id');
    }

    
}