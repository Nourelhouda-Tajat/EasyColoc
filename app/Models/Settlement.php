<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    protected $fillable = [
        'coloc_id', 
        'debtor_id', 
        'creditor_id', 
        'amount', 
        'date', 
        'status'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
        ];
    }

    public function colocation() {
        return $this->belongsTo(Colocation::class, 'coloc_id'); 
    }

    public function debtor() { 
        return $this->belongsTo(User::class, 'debtor_id'); 
    }
    
    public function creditor() { 
        return $this->belongsTo(User::class, 'creditor_id'); 
    }
}
