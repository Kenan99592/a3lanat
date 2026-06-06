<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\Billing\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function __construct(private BillingService $billingService) {}

    public function plans(): JsonResponse
    {
        $plans = $this->billingService->getPlans();

        return response()->json(['plans' => $plans]);
    }

    public function stats(Request $request): JsonResponse
    {
        $stats = $this->billingService->getBillingStats($request->user());

        return response()->json(['billing' => $stats]);
    }

    public function invoices(Request $request): JsonResponse
    {
        $invoices = $this->billingService->getInvoices($request->user());

        return response()->json(['invoices' => $invoices]);
    }

    public function createInvoice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount'               => 'required|numeric|min:1',
            'type'                 => 'required|in:subscription,ad_budget,manual',
            'tax'                  => 'nullable|numeric|min:0',
            'currency'             => 'nullable|string|max:3',
            'notes'                => 'nullable|string',
            'due_date'             => 'nullable|date',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
        ]);

        $invoice = $this->billingService->createInvoice($request->user(), $validated);

        return response()->json([
            'message' => 'تم إنشاء الفاتورة بنجاح.',
            'invoice' => $invoice,
        ], 201);
    }

    public function recordPayment(Request $request, int $invoiceId): JsonResponse
    {
        $invoice = Invoice::where('id', $invoiceId)
                          ->where('tenant_id', $request->user()->tenant_id)
                          ->first();

        if (!$invoice) {
            return response()->json(['message' => 'الفاتورة غير موجودة.'], 404);
        }

        if ($invoice->isPaid()) {
            return response()->json(['message' => 'هذه الفاتورة مدفوعة مسبقاً.'], 400);
        }

        $validated = $request->validate([
            'amount'    => 'required|numeric|min:1',
            'method'    => 'required|in:cash,bank_transfer,stripe,manual',
            'reference' => 'nullable|string',
            'notes'     => 'nullable|string',
        ]);

        $payment = $this->billingService->recordPayment($invoice, $request->user(), $validated);

        return response()->json([
            'message' => 'تم تسجيل الدفع بنجاح.',
            'payment' => $payment,
            'invoice' => $invoice->fresh(),
        ]);
    }

    public function upgradePlan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $result = $this->billingService->upgradePlan($request->user(), $validated['plan_id']);

        return response()->json($result, 201);
    }
}
