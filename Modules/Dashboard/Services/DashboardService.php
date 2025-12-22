<?php

namespace Modules\Dashboard\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\Account\Models\Account;
use Modules\Account\Enums\AccountState;
use Modules\Account\Enums\AccountType;
use Modules\Transaction\Models\Transaction;
use Modules\Transaction\Enums\TransactionStatusEnum as TransactionStatus;
use Modules\Transaction\Enums\TransactionTypeEnum as TransactionType;
use Modules\User\Models\User;
use Modules\User\Enums\UserStatus;
use Modules\AccessControl\Models\Role;

class DashboardService
{
    public function getStatistics(): array
    {
        return Cache::remember('dashboard.statistics', 300, function () {
            return [
                'users' => [
                    'total' => User::count(),
                    'active' => User::where('status', UserStatus::ACTIVE)->count(),
                    'suspended' => User::where('status', UserStatus::SUSPENDED)->count(),
                    'disabled' => User::where('status', UserStatus::DISABLED)->count(),
                    'by_role' => User::select('role_id', DB::raw('count(*) as count'))
                        ->groupBy('role_id')
                        ->with('role:id,name')
                        ->get()
                        ->map(fn($u) => [
                            'role' => $u->role?->name ?? 'Unknown',
                            'count' => $u->count
                        ])
                        ->toArray(),
                ],
                'accounts' => [
                    'total' => Account::count(),
                    'active' => Account::where('state', AccountState::ACTIVE)->count(),
                    'frozen' => Account::where('state', AccountState::FROZEN)->count(),
                    'suspended' => Account::where('state', AccountState::SUSPENDED)->count(),
                    'closed' => Account::where('state', AccountState::CLOSED)->count(),
                    'by_type' => Account::select('type', DB::raw('count(*) as count'))
                        ->groupBy('type')
                        ->get()
                        ->map(fn($a) => [
                            'type' => $a->type instanceof AccountType ? $a->type->value : (string) $a->type,
                            'count' => $a->count
                        ])
                        ->toArray(),
                    'total_balance' => (float) Account::sum('balance'),
                    'average_balance' => (float) Account::avg('balance') ?? 0,
                ],
                'transactions' => [
                    'total' => Transaction::count(),
                    'pending' => Transaction::where('status', TransactionStatus::PENDING->value)->count(),
                    'approved' => Transaction::where('status', TransactionStatus::APPROVED->value)->count(),
                    'rejected' => Transaction::where('status', TransactionStatus::REJECTED->value)->count(),
                    'by_type' => Transaction::select('type', DB::raw('count(*) as count'))
                        ->groupBy('type')
                        ->get()
                        ->map(fn($t) => [
                            'type' => $t->type instanceof TransactionType ? $t->type->value : (string) $t->type,
                            'count' => $t->count
                        ])
                        ->toArray(),
                    'total_amount' => (float) Transaction::where('status', TransactionStatus::APPROVED->value)->sum('amount'),
                ],
                'today' => [
                    'transactions' => Transaction::whereDate('created_at', today())->count(),
                    'transactions_amount' => (float) Transaction::whereDate('created_at', today())
                        ->where('status', TransactionStatus::APPROVED->value)
                        ->sum('amount'),
                    'new_accounts' => Account::whereDate('created_at', today())->count(),
                    'new_users' => User::whereDate('created_at', today())->count(),
                ],
            ];
        });
    }

    public function getSystemHealth(): array
    {
        $health = [
            'status' => 'healthy',
            'checks' => [],
            'timestamp' => now()->toIso8601String(),
        ];

        // Database connection check
        try {
            DB::connection()->getPdo();
            $health['checks']['database'] = [
                'status' => 'ok',
                'message' => 'Database connection successful',
            ];
        } catch (\Exception $e) {
            $health['status'] = 'unhealthy';
            $health['checks']['database'] = [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }

        // Cache check
        try {
            Cache::put('health_check', 'ok', 10);
            $health['checks']['cache'] = [
                'status' => 'ok',
                'message' => 'Cache system operational',
            ];
        } catch (\Exception $e) {
            $health['status'] = 'degraded';
            $health['checks']['cache'] = [
                'status' => 'warning',
                'message' => 'Cache system issue: ' . $e->getMessage(),
            ];
        }

        // Queue check (if configured)
        try {
            $queueConnection = config('queue.default');
            $health['checks']['queue'] = [
                'status' => 'ok',
                'message' => "Queue driver: {$queueConnection}",
            ];
        } catch (\Exception $e) {
            $health['checks']['queue'] = [
                'status' => 'warning',
                'message' => 'Queue configuration issue',
            ];
        }

        // Pending transactions check
        $pendingCount = Transaction::where('status', TransactionStatus::PENDING->value)
            ->where('created_at', '<', now()->subHours(24))
            ->count();
        
        if ($pendingCount > 100) {
            $health['status'] = 'degraded';
            $health['checks']['pending_transactions'] = [
                'status' => 'warning',
                'message' => "{$pendingCount} transactions pending for more than 24 hours",
            ];
        } else {
            $health['checks']['pending_transactions'] = [
                'status' => 'ok',
                'message' => "{$pendingCount} old pending transactions",
            ];
        }

        return $health;
    }

    public function getPerformanceMetrics(): array
    {
        $now = now();
        $last24Hours = $now->copy()->subDay();
        $last7Days = $now->copy()->subDays(7);
        $last30Days = $now->copy()->subDays(30);

        return [
            'response_time' => [
                'average' => $this->getAverageResponseTime(),
                'p95' => $this->getP95ResponseTime(),
            ],
            'transaction_volume' => [
                'last_24h' => Transaction::where('created_at', '>=', $last24Hours)->count(),
                'last_7d' => Transaction::where('created_at', '>=', $last7Days)->count(),
                'last_30d' => Transaction::where('created_at', '>=', $last30Days)->count(),
            ],
            'transaction_value' => [
                'last_24h' => (float) Transaction::where('created_at', '>=', $last24Hours)
                    ->where('status', TransactionStatus::APPROVED->value)
                    ->sum('amount'),
                'last_7d' => (float) Transaction::where('created_at', '>=', $last7Days)
                    ->where('status', TransactionStatus::APPROVED->value)
                    ->sum('amount'),
                'last_30d' => (float) Transaction::where('created_at', '>=', $last30Days)
                    ->where('status', TransactionStatus::APPROVED->value)
                    ->sum('amount'),
            ],
            'account_growth' => [
                'last_24h' => Account::where('created_at', '>=', $last24Hours)->count(),
                'last_7d' => Account::where('created_at', '>=', $last7Days)->count(),
                'last_30d' => Account::where('created_at', '>=', $last30Days)->count(),
            ],
            'user_growth' => [
                'last_24h' => User::where('created_at', '>=', $last24Hours)->count(),
                'last_7d' => User::where('created_at', '>=', $last7Days)->count(),
                'last_30d' => User::where('created_at', '>=', $last30Days)->count(),
            ],
        ];
    }

    public function getActivityMonitoring(int $limit = 50): array
    {
        $recentTransactions = Transaction::with(['fromAccount', 'toAccount'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'type' => $t->type instanceof TransactionType ? $t->type->value : (string) $t->type,
                'status' => $t->status instanceof TransactionStatus ? $t->status->value : (string) $t->status,
                'amount' => (float) $t->amount,
                'from_account' => $t->fromAccount?->uuid,
                'to_account' => $t->toAccount?->uuid,
                'created_at' => $t->created_at->toIso8601String(),
            ]);

        $recentAccounts = Account::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($a) => [
                'uuid' => $a->uuid,
                'type' => $a->type instanceof AccountType ? $a->type->value : (string) $a->type,
                'state' => $a->state instanceof AccountState ? $a->state->value : (string) $a->state,
                'balance' => (float) $a->balance,
                'customer_id' => $a->customer_id,
                'created_at' => $a->created_at->toIso8601String(),
            ]);

        return [
            'recent_transactions' => $recentTransactions,
            'recent_accounts' => $recentAccounts,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    public function getFinancialOverview(): array
    {
        $now = now();
        $today = $now->copy()->startOfDay();
        $thisWeek = $now->copy()->startOfWeek();
        $thisMonth = $now->copy()->startOfMonth();
        $thisYear = $now->copy()->startOfYear();

        return [
            'total_assets' => [
                'all_accounts' => (float) Account::sum('balance'),
                'active_accounts' => (float) Account::where('state', AccountState::ACTIVE)->sum('balance'),
            ],
            'transactions' => [
                'today' => [
                    'count' => Transaction::where('created_at', '>=', $today)->count(),
                    'amount' => (float) Transaction::where('created_at', '>=', $today)
                        ->where('status', TransactionStatus::APPROVED->value)
                        ->sum('amount'),
                ],
                'this_week' => [
                    'count' => Transaction::where('created_at', '>=', $thisWeek)->count(),
                    'amount' => (float) Transaction::where('created_at', '>=', $thisWeek)
                        ->where('status', TransactionStatus::APPROVED->value)
                        ->sum('amount'),
                ],
                'this_month' => [
                    'count' => Transaction::where('created_at', '>=', $thisMonth)->count(),
                    'amount' => (float) Transaction::where('created_at', '>=', $thisMonth)
                        ->where('status', TransactionStatus::APPROVED->value)
                        ->sum('amount'),
                ],
                'this_year' => [
                    'count' => Transaction::where('created_at', '>=', $thisYear)->count(),
                    'amount' => (float) Transaction::where('created_at', '>=', $thisYear)
                        ->where('status', TransactionStatus::APPROVED->value)
                        ->sum('amount'),
                ],
            ],
            'by_type' => [
                'deposits' => (float) Transaction::where('type', TransactionType::DEPOSIT->value)
                    ->where('status', TransactionStatus::APPROVED->value)
                    ->sum('amount'),
                'withdrawals' => (float) Transaction::where('type', TransactionType::WITHDRAWAL->value)
                    ->where('status', TransactionStatus::APPROVED->value)
                    ->sum('amount'),
                'transfers' => (float) Transaction::where('type', TransactionType::TRANSFER->value)
                    ->where('status', TransactionStatus::APPROVED->value)
                    ->sum('amount'),
            ],
            'account_distribution' => Account::select('type', DB::raw('sum(balance) as total'))
                ->groupBy('type')
                ->get()
                ->map(fn($a) => [
                    'type' => $a->type instanceof AccountType ? $a->type->value : (string) $a->type,
                    'total_balance' => (float) $a->total,
                ])
                ->toArray(),
        ];
    }

    private function getAverageResponseTime(): float
    {
        // This would typically come from APM or logging system
        // For now, return a mock value
        return 150.5; // milliseconds
    }

    private function getP95ResponseTime(): float
    {
        // This would typically come from APM or logging system
        // For now, return a mock value
        return 250.0; // milliseconds
    }
}

