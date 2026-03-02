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

    public function expenses(){
        return $this->hasMany(Expense::class, 'coloc_id');
    }

    public function settlements() {
        return $this->hasMany(Settlement::class, 'coloc_id');
    }


    public function calculateDebts()
    {
        $memberships = $this->memberships()->with('user')->get();
        $balances = [];

        foreach ($memberships as $m) {
            $balances[$m->user_id] = [
                'user' => $m->user,
                'paid_expenses' => 0, 'share_expenses' => 0,
                'paid_settlements' => 0, 'received_settlements' => 0,
                'net' => 0
            ];
        }

        foreach ($this->expenses as $expense) {
            $expenseDate = \Carbon\Carbon::parse($expense->date)->endOfDay();

            $activeMembersAtTime = $memberships->filter(function($m) use ($expenseDate) {
                $joinedAt = \Carbon\Carbon::parse($m->joined_at)->startOfDay();
                $leftAt = $m->left_at ? \Carbon\Carbon::parse($m->left_at)->endOfDay() : null;

                return $joinedAt <= $expenseDate && ($leftAt === null || $leftAt >= $expenseDate);
            });

            if ($activeMembersAtTime->isEmpty()) {
                $activeMembersAtTime = $memberships->whereNull('left_at');
            }

            if ($activeMembersAtTime->isEmpty()) continue;

            $split = $expense->amount / $activeMembersAtTime->count();
            
            if (isset($balances[$expense->payer_id])) {
                $balances[$expense->payer_id]['paid_expenses'] += $expense->amount;
            }

            foreach ($activeMembersAtTime as $m) {
                $balances[$m->user_id]['share_expenses'] += $split;
            }
        }

        foreach ($this->settlements()->where('status', 'paid')->get() as $settlement) {
            if (isset($balances[$settlement->debtor_id])) {
                $balances[$settlement->debtor_id]['paid_settlements'] += $settlement->amount;
            }
            if (isset($balances[$settlement->creditor_id])) {
                $balances[$settlement->creditor_id]['received_settlements'] += $settlement->amount;
            }
        }

        $creditors = []; 
        $debtors = [];   

        foreach ($balances as $id => &$b) {
            $b['net'] = ($b['paid_expenses'] + $b['paid_settlements']) - ($b['share_expenses'] + $b['received_settlements']);
            
            $net = round($b['net'], 2);
            if ($net > 0) $creditors[$id] = $net;
            elseif ($net < 0) $debtors[$id] = abs($net);
        }

        $suggestedSettlements = [];
        arsort($creditors); 
        arsort($debtors);   

        foreach ($debtors as $dId => $dAmount) {
            foreach ($creditors as $cId => $cAmount) {
                if (round($dAmount, 2) <= 0) break;
                if (round($cAmount, 2) <= 0) continue;

                $amountToPay = min($dAmount, $cAmount);

                $suggestedSettlements[] = [
                    'debtor_id' => $dId,
                    'debtor' => $balances[$dId]['user'],
                    'creditor_id' => $cId,
                    'creditor' => $balances[$cId]['user'],
                    'amount' => round($amountToPay, 2)
                ];

                $dAmount -= $amountToPay;
                $creditors[$cId] -= $amountToPay;
                $debtors[$dId] = $dAmount;
            }
        }

        return ['balances' => $balances, 'suggested' => $suggestedSettlements];
    }


}
