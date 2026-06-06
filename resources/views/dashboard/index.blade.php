@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('content')

<div x-data="dashboard()" x-init="init()">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">لوحة التحكم</h1>
            <p class="text-gray-400 text-sm mt-1">مرحباً بك في منصة الإعلانات</p>
        </div>
        <a href="/campaigns/create" class="btn-primary">+ حملة جديدة</a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <p class="text-gray-400 text-sm">إجمالي الظهورات</p>
            <p class="text-2xl font-bold gold mt-1" x-text="formatNumber(stats.totals?.impressions)">0</p>
        </div>
        <div class="stat-card">
            <p class="text-gray-400 text-sm">إجمالي النقرات</p>
            <p class="text-2xl font-bold gold mt-1" x-text="formatNumber(stats.totals?.clicks)">0</p>
        </div>
        <div class="stat-card">
            <p class="text-gray-400 text-sm">إجمالي الإنفاق</p>
            <p class="text-2xl font-bold gold mt-1" x-text="'$' + (stats.totals?.spend || 0)">$0</p>
        </div>
        <div class="stat-card">
            <p class="text-gray-400 text-sm">الحملات النشطة</p>
            <p class="text-2xl font-bold gold mt-1" x-text="stats.campaigns?.active || 0">0</p>
        </div>
    </div>

    {{-- Averages --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="card text-center">
            <p class="text-gray-400 text-sm">متوسط CTR</p>
            <p class="text-xl font-bold mt-1" x-text="(stats.averages?.ctr || 0) + '%'">0%</p>
        </div>
        <div class="card text-center">
            <p class="text-gray-400 text-sm">متوسط CPM</p>
            <p class="text-xl font-bold mt-1" x-text="'$' + (stats.averages?.cpm || 0)">$0</p>
        </div>
        <div class="card text-center">
            <p class="text-gray-400 text-sm">متوسط CPC</p>
            <p class="text-xl font-bold mt-1" x-text="'$' + (stats.averages?.cpc || 0)">$0</p>
        </div>
    </div>

    {{-- Campaigns Table --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold">الحملات الأخيرة</h2>
            <a href="/campaigns" class="text-sm gold hover:underline">عرض الكل</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>اسم الحملة</th>
                    <th>الهدف</th>
                    <th>الميزانية</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="c in campaigns" :key="c.id">
                    <tr>
                        <td x-text="c.name"></td>
                        <td x-text="c.objective"></td>
                        <td x-text="'$' + c.budget"></td>
                        <td>
                            <span :class="c.status === 'ACTIVE' ? 'text-green-400' : 'text-yellow-400'" x-text="c.status === 'ACTIVE' ? 'نشطة' : 'متوقفة'"></span>
                        </td>
                    </tr>
                </template>
                <tr x-show="campaigns.length === 0">
                    <td colspan="4" class="text-center text-gray-400 py-4">لا توجد حملات بعد</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<script>
function dashboard() {
    return {
        stats: {},
        campaigns: [],
        async init() {
            const [statsRes, campaignsRes] = await Promise.all([
                api('GET', '/analytics/dashboard'),
                api('GET', '/campaigns'),
            ]);
            this.stats     = statsRes.stats || {};
            this.campaigns = campaignsRes.campaigns?.data?.slice(0, 5) || [];
        },
        formatNumber(n) {
            return n ? n.toLocaleString('ar') : '0';
        }
    }
}
</script>
@endsection
