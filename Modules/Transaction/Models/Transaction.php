<?php

namespace Modules\Transaction\Models;

use Modules\Transaction\Enums\TransactionStatus;
use Modules\Transaction\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Account\Models\Account;
use App\Models\User;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'amount'        => 'decimal:4',
        'type'          => TransactionType::class,
        'status'        => TransactionStatus::class,
        'is_scheduled'  => 'boolean',
        'scheduled_at'  => 'datetime',
    ];

    public function isApproved(): bool
    {
        return $this->status === TransactionStatus::APPROVED;
    }

    public function approve(int $approvedBy)
    {
        $this->approved_by = $approvedBy;
        $this->approved_at = now();
        $this->save();
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
