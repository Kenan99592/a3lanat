<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Str;

class BillingService
{
    public function getInvoices(User $user): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Invoice::where('tenant_id', $user->tenant_id)
                      ->with(['subscriptionPlan', 'payments'])
                      ->latest()
                      ->paginate(10);
    }

    public function createInvoice(User $user, array $data): Invoice
    {
        $amount = $data['amount'];
        $tax    = $data['tax'] ?? 0;
        $total  = $amount + $tax;

        return Invoice::create([
            'user_id'              => $user->id,
            'tenant_id'            => $user->tenant_id,
            'subscription_plan_id' => $data['subscription_plan_id'] ?? null,
            'invoice_number'       => 'INV-' . strtoupper(Str::random(8)),
            'status'               => 'pending',
            'type'                 => $data['type'] ?? 'subscription',
            'amount'               => $amount,
            'tax'                  => $tax,
            'total'                => $total,
            'currency'             => $data['currency'] ?? 'USD',
            'notes'                => $data['notes'] ?? null,
            'due_date'             => $data['due_date'] ?? now()->addDays(7),
        ]);
    }

    public function recordPayment(Invoice $invoice, User $user, array $data): Payment
    {
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'user_id'    => $user->id,
            'tenant_id'  => $user->tenant_id,
            'amount'     => $data['amount'],
            'currency'   => $data['currency'] ?? 'USD',
            'method'     => $data['method'] ?? 'manual',
            'status'     => 'completed',
            'reference'  => $data['reference'] ?? null,
            'notes'      => $data['notes'] ?? null,
            'paid_at'    => now(),
        ]);

        $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');

        if ($totalPaid >= $invoice->total) {
            $invoice->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);
        }

        return $payment;
    }

    public function upgradePlan(User $user, int $planId): array
    {
        $plan = SubscriptionPlan::findOrFail($planId);

        $invoice = $this->createInvoice($user, [
            'subscription_plan_id' => $plan->id,
            'type'                 => 'subscription',
            'amount'               => $plan->price,
            'notes'                => "اشتراك خطة {$plan->name}",
            'due_date'             => now()->addDays(3),
        ]);

        return [
            'plan'    => $plan,
            'invoice' => $invoice,
            'message' => "تم إنشاء فاتورة لخطة {$plan->name}. يرجى إتمام الدفع.",
        ];
    }

    public function getBillingStats(User $user): array
    {
        $tenantId = $user->tenant_id;

        $totalPaid    = Invoice::where('tenant_id', $tenantId)->where('status', 'paid')->sum('total');
        $totalPending = Invoice::where('tenant_id', $tenantId)->where('status', 'pending')->sum('total');
        $totalOverdue = Invoice::where('tenant_id', $tenantId)
                               ->where('status', 'pending')
                               ->where('due_date', '<', now())
                               ->sum('total');

        return [
            'total_paid'    => round($totalPaid, 2),
            'total_pending' => round($totalPending, 2),
            'total_overdue' => round($totalOverdue, 2),
            'current_plan'  => $user->subscriptionPlan,
            'trial_ends_at' => $user->trial_ends_at,
            'is_on_trial'   => $user->isOnTrial(),
        ];
    }

    public function getPlans(): \Illuminate\Database\Eloquent\Collection
    {
        return SubscriptionPlan::where('is_active', true)->get();
    }
}
