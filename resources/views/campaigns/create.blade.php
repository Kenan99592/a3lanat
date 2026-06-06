@extends('layouts.app')
@section('title', 'حملة جديدة')
@section('content')

<div x-data="createCampaign()" class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="/campaigns" class="text-gray-400 hover:text-white">← رجوع</a>
        <h1 class="text-2xl font-bold">إنشاء حملة جديدة</h1>
    </div>

    <div class="card">
        <div x-show="success" class="mb-4 p-3 rounded-lg text-sm" style="background:rgba(34,197,94,0.1);color:#4ade80;border:1px solid #22c55e">
            ✅ تم إنشاء الحملة بنجاح!
        </div>
        <div x-show="error" class="mb-4 p-3 rounded-lg text-sm" style="background:rgba(239,68,68,0.1);color:#f87171;border:1px solid #ef4444">
            <span x-text="error"></span>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1">اسم الحملة</label>
                <input type="text" x-model="form.name" placeholder="حملة رمضان 2026">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">هدف الحملة</label>
                <select x-model="form.objective">
                    <option value="AWARENESS">الوعي بالعلامة التجارية</option>
                    <option value="TRAFFIC">زيارات الموقع</option>
                    <option value="ENGAGEMENT">التفاعل</option>
                    <option value="LEADS">جمع العملاء المحتملين</option>
                    <option value="SALES">المبيعات</option>
                    <option value="APP_PROMOTION">ترويج التطبيق</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">نوع الميزانية</label>
                <select x-model="form.budget_type">
                    <option value="daily">يومية</option>
                    <option value="lifetime">إجمالية</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">الميزانية ($)</label>
                <input type="number" x-model="form.budget" placeholder="50" min="1">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">تاريخ البداية</label>
                    <input type="date" x-model="form.start_date">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">تاريخ النهاية</label>
                    <input type="date" x-model="form.end_date">
                </div>
            </div>
            <button @click="submit()" :disabled="loading" class="btn-primary w-full">
                <span x-show="!loading">إنشاء الحملة</span>
                <span x-show="loading">جاري الإنشاء...</span>
            </button>
        </div>
    </div>
</div>

<script>
function createCampaign() {
    return {
        form: { name: '', objective: 'AWARENESS', budget_type: 'daily', budget: '', start_date: '', end_date: '' },
        error: '', success: false, loading: false,
        async submit() {
            this.loading = true; this.error = ''; this.success = false;
            const res = await api('POST', '/campaigns', this.form);
            if (res.campaign) {
                this.success = true;
                this.form = { name: '', objective: 'AWARENESS', budget_type: 'daily', budget: '', start_date: '', end_date: '' };
                setTimeout(() => window.location.href = '/campaigns', 1500);
            } else {
                this.error = res.message || 'حدث خطأ';
            }
            this.loading = false;
        }
    }
}
</script>
@endsection
