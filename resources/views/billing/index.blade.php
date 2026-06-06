@extends('layouts.app')
@section('title', 'الفوترة')
@section('content')

<div x-data="billing()" x-init="init()">

    <h1 class="text-2xl font-bold mb-6">الفوترة والاشتراكات</h1>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
            <p class="text-gray-400 text-sm">إجمالي المدفوع</p>
            <p class="text-2xl font-bold gold mt-1" x-text="'$' + stats.total_paid">$0</p>
        </div>
        <div class="stat-card">
            <p class="text-gray-400 text-sm">المبالغ المعلقة</p>
            <p class="text-2xl font-bold text-yellow-400 mt-1" x-text="'$' + stats.total_pending">$0</p>
        </div>
        <div class="stat-card">
            <p class="text-gray-400 text-sm">الخطة الحالية</p>
            <p class="text-xl font-bold mt-1" x-text="stats.current_plan?.name || 'تجريبية'">تجريبية</p>
        </div>
    </div>

    {{-- Plans --}}
    <div class="card mb-6">
        <h2 class="text-lg font-bold mb-4">خطط الاشتراك</h2>
        <div class="grid grid-cols-4 gap-4">
            <template x-for="plan in plans" :key="plan.id">
                <div class="p-4 rounded-lg border" style="border-color:#2d2d4e; background:#2d2d4e">
                    <h3 class="font-bold text-lg" x-text="plan.name"></h3>
                    <p class="text-2xl font-bold gold my-2" x-text="plan.price == 0 ? 'مجاني' : '$' + plan.price + '/شهر'"></p>
                    <ul class="text-xs text-gray-400 space-y-1 mb-3">
                        <li x-text="'حملات: ' + plan.max_campaigns"></li>
                        <li x-text="'إنفاق: $' + plan.max_monthly_spend"></li>
                    </ul>
                    <button @click="upgrade(plan.id)" class="btn-primary w-full text-sm" x-show="plan.price > 0">ترقية</button>
                </div>
            </template>
        </div>
    </div>

    {{-- Invoices --}}
    <div class="card">
        <h2 class="text-lg font-bold mb-4">الفواتير</h2>
        <table>
            <thead>
                <tr>
                    <th>رقم الفاتورة</th>
                    <th>النوع</th>
                    <th>المبلغ</th>
                    <th>الحالة</th>
                    <th>تاريخ الاستحقاق</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="inv in invoices" :key="inv.id">
                    <tr>
                        <td x-text="inv.invoice_number"></td>
                        <td x-text="inv.type"></td>
                        <td x-text="'$' + inv.total"></td>
                        <td>
                            <span :class="{
                                'text-green-400': inv.status === 'paid',
                                'text-yellow-400': inv.status === 'pending',
                                'text-red-400': inv.status === 'overdue'
                            }" x-text="inv.status === 'paid' ? 'مدفوعة' : inv.status === 'pending' ? 'معلقة' : 'متأخرة'"></span>
                        </td>
                        <td x-text="inv.due_date"></td>
                    </tr>
                </template>
                <tr x-show="invoices.length === 0">
                    <td colspan="5" class="text-center text-gray-400 py-4">لا توجد فواتير</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function billing() {
    return {
        stats: {}, plans: [], invoices: [],
        async init() {
            const [statsRes, plansRes, invoicesRes] = await Promise.all([
                api('GET', '/billing/stats'),
                api('GET', '/billing/plans'),
                api('GET', '/billing/invoices'),
            ]);
            this.stats    = statsRes.billing || {};
            this.plans    = plansRes.plans || [];
            this.invoices = invoicesRes.invoices?.data || [];
        },
        async upgrade(planId) {
            const res = await api('POST', '/billing/upgrade', { plan_id: planId });
            alert(res.message);
            this.init();
        }
    }
}
</script>
@endsection
