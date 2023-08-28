<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nette\Schema\Expect;

class Expense extends Model
{
    use HasFactory;

    const TYPES = ['essential', 'superfluous', 'investment', 'emergency'];

    protected $with = ['installments'];

    protected $fillable = [
        'name',
        'type',
        'value',
        'user_id',
        'date',
        'description',
        'category',
        'is_recurrence',
        'is_installment',
        'start_installment_at',
        'end_installment_at',
        'parent_id',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['type'] ?? false, function ($query, $type) {
            $query->where('type', $type);
        });

        $query->when($filters['description'] ?? false, function ($query, $description) {
            $query->where('name', 'like','%'.$description.'%');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function installments()
    {
        return $this->hasMany(Expense::class, 'parent_id')->whereNotNull('parent_id');
    }
}
