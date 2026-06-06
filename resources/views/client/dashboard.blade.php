<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة العميل</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:#0f0f1a; min-height:100vh">

<div x-data="clientDashboard()" x-init="init()">

    {{-- Header --}}
    <header class="flex items-center justify-between p-6 border-b" style="border-color:#2d2d4e; background:#1a1a2e">
        <h1 class="text-xl font-bold gold">🎯 نتائج إعلاناتك</h1>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-400" x-text="userName"></span>
            <button @click="logout()" class="btn-secondary text-sm">خروج</button>
        </div>
    </header>

    <div class="p-6">

        {{-- Trial Notice --}}
        <div x-show="isOnTrial" class="mb-6 p-4 rounded-lg" style="background:rgba(212,160,23,0.1); border:1px solid #d4a017">
            <p class="text-sm">⏰ أنت في الفترة التجريبية المجانية. تنتهي في: <strong x-text="trialEndsAt"></strong></p>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-4 mb-6 md:grid-cols-4">
            <div class="stat-card text-center">
                <p class="text-gray-400 text-sm mb-1">الظهورات</p>
                <p class="text-2xl font-bold gold" x-text="formatNum(stats.totals?.impressions)">0</p>
            </div>
            <div class="stat-card text-center">
                <p class="text-gray-400 text-sm mb-1">الوصول</p>
                <p class="text-2xl font-bold gold" x-text="formatNum(stats.totals?.reach)">0</p>
            </div>
            <div class="stat-card text-center">
                <p class="text-gray-400 text-sm mb-1">النقرات</p>
                <p class="text-2xl font-bold gold" x-text="formatNum(stats.totals?.clicks)">0</p>
            </div>
            <div class="stat-card text-center">
                <p class="text-gray-400 text-sm mb-1">الإنفاق</p>
                <p class="text-2xl font-bold gold" x-text="'$' + (stats.totals?.spend || 0)">$0</p>
            </div>
        </div>

        {{-- Campaigns --}}
        <div class="card mb-6">
            <h2 class="text-lg font-bold mb-4">حملاتك الإعلانية</h2>
            <div class="space-y-3">
                <template x-for="c in campaigns" :key="c.id">
                    <div class="p-4 rounded-lg cursor-pointer" 
                         style="background:#2d2d4e"
                         @click="selectCampaign(c)">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium" x-text="c.name"></p>
                                <p class="text-sm text-gray-400 mt-1" x-text="'الهدف: ' + objectiveAr(c.objective)"></p>
                            </div>
                            <div class="text-left">
                                <span :class="c.status === 'ACTIVE' ? 'text-green-400' : 'text-yellow-400'"
                                      class="text-sm font-bold"
                                      x-text="c.status === 'ACTIVE' ? '🟢 نشطة' : '🟡 متوقفة'"></span>
                                <p class="text-sm text-gray-400 mt-1" x-text="'$' + c.budget + ' / ' + (c.budget_type === 'daily' ? 'يوم' : 'إجمالي')"></p>
                            </div>
                        </div>
                    </div>
                </template>
                <div x-show="campaigns.length === 0" class="text-center text-gray-400 py-6">
                    لا توجد حملات نشطة حالياً
                </div>
            </div>
        </div>

        {{-- Campaign Details --}}
        <div class="card mb-6" x-show="selectedCampaign">
            <h2 class="text-lg font-bold mb-4">تفاصيل: <span class="gold" x-text="selectedCampaign?.name"></span></h2>

            <div class="flex gap-2 mb-4">
                <button @click="loadInsights('daily')" 
                        :class="period === 'daily' ? 'btn-primary' : 'btn-secondary'"
                        class="text-sm">آخر 7 أيام</button>
                <button @click="loadInsights('weekly')"
                        :class="period === 'weekly' ? 'btn-primary' : 'btn-secondary'"
                        class="text-sm">آخر 4 أسابيع</button>
                <button @click="loadInsights('monthly')"
                        :class="period === 'monthly' ? 'btn-primary' : 'btn-secondary'"
                        class="text-sm">آخر 3 أشهر</button>
            </div>

            <div class="grid grid-cols-4 gap-3 mb-4" x-show="insights.totals">
                <div class="p-3 rounded text-center" style="background:#2d2d4e">
                    <p class="text-xs text-gray-400">الظهورات</p>
                    <p class="font-bold gold" x-text="formatNum(insights.totals?.impressions)"></p>
                </div>
                <div class="p-3 rounded text-center" style="background:#2d2d4e">
                    <p class="text-xs text-gray-400">النقرات</p>
                    <p class="font-bold gold" x-text="formatNum(insights.totals?.clicks)"></p>
                </div>
                <div class="p-3 rounded text-center" style="background:#2d2d4e">
                    <p class="text-xs text-gray-400">الإنفاق</p>
                    <p class="font-bold gold" x-text="'$' + (insights.totals?.spend || 0)"></p>
                </div>
                <div class="p-3 rounded text-center" style="background:#2d2d4e">
                    <p class="text-xs text-gray-400">التحويلات</p>
                    <p class="font-bold gold" x-text="insights.totals?.conversions || 0"></p>
                </div>
            </div>

            {{-- Table --}}
            <table>
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>الظهورات</th>
                        <th>النقرات</th>
                        <th>الإنفاق</th>
                        <th>CTR</th>
                        <th>CPM</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="row in insights.insights" :key="row.id">
                        <tr>
                            <td x-text="row.date"></td>
                            <td x-text="formatNum(row.impressions)"></td>
                            <td x-text="formatNum(row.clicks)"></td>
                            <td x-text="'$' + row.spend"></td>
                            <td x-text="row.ctr + '%'"></td>
                            <td x-text="'$' + row.cpm"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Invoices --}}
        <div class="card">
            <h2 class="text-lg font-bold mb-4">فواتيرك</h2>
            <table>
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                        <th>تاريخ الاستحقاق</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="inv in invoices" :key="inv.id">
                        <tr>
                            <td x-text="inv.invoice_number"></td>
                            <td x-text="'$' + inv.total"></td>
                            <td>
                                <span :class="{
                                    'text-green-400': inv.status === 'paid',
                                    'text-yellow-400': inv.status === 'pending',
                                    'text-red-400': inv.status === 'overdue'
                                }" x-text="inv.status === 'paid' ? '✅ مدفوعة' : inv.status === 'pending' ? '⏳ معلقة' : '❌ متأخرة'"></span>
                            </td>
                            <td x-text="inv.due_date || '-'"></td>
                        </tr>
                    </template>
                    <tr x-show="invoices.length === 0">
                        <td colspan="4" class="text-center text-gray-400 py-4">لا توجد فواتير</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
function clientDashboard() {
    return {
        userName: '',
        isOnTrial: false,
        trialEndsAt: '',
        stats: {},
        campaigns: [],
        invoices: [],
        selectedCampaign: null,
        insights: {},
        period: 'monthly',

        async init() {
            const token = localStorage.getItem('token');
            if (!token) { window.location.href = '/login'; return; }

            const [meRes, statsRes, campaignsRes, invoicesRes] = await Promise.all([
                api('GET', '/auth/me'),
                api('GET', '/analytics/dashboard'),
                api('GET', '/campaigns'),
                api('GET', '/billing/invoices'),
            ]);

            if (meRes.user) {
                this.userName   = meRes.user.name;
                this.isOnTrial  = meRes.user.trial_ends_at && new Date(meRes.user.trial_ends_at) > new Date();
                this.trialEndsAt = meRes.user.trial_ends_at ? new Date(meRes.user.trial_ends_at).toLocaleDateString('ar') : '';
            }

            this.stats     = statsRes.stats || {};
            this.campaigns = campaignsRes.campaigns?.data || [];
            this.invoices  = invoicesRes.invoices?.data || [];
        },

        async selectCampaign(c) {
            this.selectedCampaign = c;
            await this.loadInsights('monthly');
        },

        async loadInsights(period) {
            this.period = period;
            const res = await api('GET', `/analytics/campaigns/${this.selectedCampaign.id}?period=${period}`);
            this.insights = res.data || {};
        },

        objectiveAr(obj) {
            const map = {
                'AWARENESS': 'الوعي', 'TRAFFIC': 'الزيارات',
                'ENGAGEMENT': 'التفاعل', 'LEADS': 'العملاء المحتملين',
                'SALES': 'المبيعات', 'APP_PROMOTION': 'ترويج التطبيق'
            };
            return map[obj] || obj;
        },

        formatNum(n) { return n ? Number(n).toLocaleString('ar') : '0'; },

        async logout() {
            await api('POST', '/auth/logout');
            localStorage.removeItem('token');
            window.location.href = '/login';
        }
    }
}
</script>
</body>
</html>
