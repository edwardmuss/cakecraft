<?php

namespace App\Services\Billing;

use App\Models\Account;
use App\Models\AccountLedgerEntry;
use Illuminate\Support\Facades\DB;

class AccountLedgerService
{
    /**
     * Apply a balance change atomically and record it once.
     * amount_minor: +credit, -debit
     */
    public function apply(
        int $branchId,
        ?int $createdBy,
        int $amountMinor,
        string $currency,
        string $source,
        string $eventType,
        string $idempotencyKey,
        ?string $reference = null,
        ?string $description = null,
        array $meta = []
    ): AccountLedgerEntry {
        return DB::transaction(function () use (
            $branchId,
            $createdBy,
            $amountMinor,
            $currency,
            $source,
            $eventType,
            $idempotencyKey,
            $reference,
            $description,
            $meta
        ) {
            // idempotency
            $existing = AccountLedgerEntry::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) return $existing;

            /** @var Account $branch */
            $branch = Account::lockForUpdate()->findOrFail($branchId);

            $before = (int) $branch->balance_minor;
            $after  = $before + $amountMinor;

            // You can enforce no-negative here if you want:
            // if ($after < 0) throw new \RuntimeException('Insufficient balance');

            $branch->balance_minor = $after;
            $branch->save();

            return AccountLedgerEntry::create([
                'branch_id'            => $branchId,
                'created_by'            => $createdBy,
                'amount_minor'          => $amountMinor,
                'balance_before_minor'  => $before,
                'balance_after_minor'   => $after,
                'currency'              => $currency,
                'source'                => $source,
                'event_type'            => $eventType,
                'idempotency_key'       => $idempotencyKey,
                'reference'             => $reference,
                'description'           => $description,
                'meta'                  => $meta ?: null,
            ]);
        });
    }
}
