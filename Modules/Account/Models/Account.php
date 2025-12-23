<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Account\Enums\AccountState;
use Modules\Account\Enums\AccountType;
use Modules\Account\Patterns\States\AccountStateInterface;
use Modules\User\Models\User;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'account_number',
        'customer_id',
        'parent_account_id',
        'type',
        'state',
        'balance',
        'currency',
        'meta',
        'opened_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'balance'   => 'decimal:4',
            'type'      => AccountType::class,
            'state'     => AccountState::class,
            'opened_at' => 'datetime',
            'meta'      => 'array',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_account_id');
    }

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_account_id');
    }

    public function getStateInstance(): AccountStateInterface
    {
        return $this->state->resolve();
    }

    public function isComposite(): bool
    {
        return $this->children()->exists();
    }

    public function typeDecorator()
    {
        $decoratorClass = $this->type->decoratorClass();
        return new $decoratorClass($this);
    }
}
