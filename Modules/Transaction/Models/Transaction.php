<?php

namespace Modules\Transaction\Models;

use App\Modules\Transaction\Enums\TransactionStatus;
use App\Modules\Transaction\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Account\Models\Account;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'balance',
        'type',
        'status',
    ];

    protected $casts = [
        'type' => TransactionType::class,
        'status' => TransactionStatus::class,
    ];

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }
}
