<?php

namespace App\Observers;

use App\Enums\SystemEvents;
use App\Enums\TransactionStatus;
use App\Events\SystemEvent;
use App\Transaction;

class TransactionObserver
{
    public function saved(Transaction $transaction) {
        if (!$transaction->isDirty('status')) {
            return;
        }
        switch ($transaction->status) {
            case TransactionStatus::COMPLETED:
                event(new SystemEvent(SystemEvents::TRANSACTION_COMPLETED,
                    $transaction));
                break;
            case TransactionStatus::FAILED:
                event(new SystemEvent(SystemEvents::TRANSACTION_FAILED,
                    $transaction));
                break;
            case TransactionStatus::REFUNDED:
                event(new SystemEvent(SystemEvents::TRANSACTION_REFUND,
                    $transaction));
                break;
            case TransactionStatus::PROCESSING:
                event(new SystemEvent(SystemEvents::TRANSACTION_PENDING,
                    $transaction));
                break;
            default:
                break;
        }
    }
}
