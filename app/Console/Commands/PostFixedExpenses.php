<?php

namespace App\Console\Commands;

use App\Models\FixedExpense;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('refat:post-fixed-expenses')]
#[Description('Auto-post due fixed expenses as transactions (run daily)')]
class PostFixedExpenses extends Command
{
    public function handle(): int
    {
        $due = FixedExpense::query()
            ->where('is_active', true)
            ->where('auto_post', true)
            ->whereDate('next_run_at', '<=', today())
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', today());
            })
            ->get();

        $count = 0;
        foreach ($due as $fe) {
            $tx = $fe->postTransaction();
            $this->info("Posted {$tx->reference_no} for {$fe->name} ({$fe->amount} {$fe->currency})");
            $count++;
        }

        $this->info("Total: $count fixed expense(s) posted.");
        return self::SUCCESS;
    }
}
