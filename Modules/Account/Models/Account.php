<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Account\Enums\AccountState;
use Modules\Account\Enums\AccountType;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid','customer_id','type','balance','currency','state','parent_account_id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'balance' => 'decimal:4',
            'type' => AccountType::class,
            'state' => AccountState::class,
        ];
    }

    public function children()
    {
        return $this->hasMany(AccountModel::class, 'parent_account_id');
    }

    public function parent()
    {
        return $this->belongsTo(AccountModel::class, 'parent_account_id');
    }
}
