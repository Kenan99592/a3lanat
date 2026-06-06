@extends('layouts.app')
@section('title', 'الحملات')
@section('content')

<div x-data="campaigns()" x-init="init()">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">الحملات الإعلانية</h1>
        <a href="/campaigns/create" class="btn-primary">+ حملة جديدة</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>الحملة</th>
                    <th>الهدف</th>
                    <th>نوع الميزانية</th>
                    <th>الميزانية</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="c in list" :key="c.id">
                    <tr>
                        <td x-text="c.name"></td>
                        <td x-text="c.objective"></td>
                        <td x-text="c.budget_type === 'daily' ? 'يومية' : 'إجمالية'"></td>
                        <td x-text="'$' + c.budget"></td>
                        <td>
                            <span :class="c.status === 'ACTIVE' ? 'text-green-400' : 'text-yellow-400'"
                                  x-text="c.status === 'ACTIVE' ? 'نشطة' : 'متوقفة'"></span>
                        </td>
                        <td class="flex gap-2">
                            <button @click="toggle(c)" class="text-xs btn-secondary py-1 px-2"
                                x-text="c.status === 'ACTIVE' ? 'إيقاف' : 'تشغيل'"></button>
                            <button @click="del(c.id)" class="text-xs py-1 px-2 rounded" style="background:rgba(239,68,68,0.1);color:#f87171">حذف</button>
                        </td>
                    </tr>
                </template>
                <tr x-show="list.length === 0">
                    <td colspan="6" class="text-center text-gray-400 py-6">لا توجد حملات. <a href="/campaigns/create" class="gold">أنشئ حملة الآن</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function campaigns() {
    return {
        list: [],
        async init() {
            const res = await api('GET', '/campaigns');
            this.list = res.campaigns?.data || [];
        },
        async toggle(c) {
            const res = await api('POST', `/campaigns/${c.id}/toggle`);
            c.status = res.status;
        },
        async del(id) {
            if (!confirm('هل تريد حذف هذه الحملة؟')) return;
            await api('DELETE', `/campaigns/${id}`);
            this.list = this.list.filter(c => c.id !== id);
        }
    }
}
</script>
@endsection
