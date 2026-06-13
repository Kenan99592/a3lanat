@extends('layouts.app')
@section('title', 'نشر إعلان جديد')
@section('content')

<div x-data="launchCampaign()" class="max-w-3xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="/campaigns" class="text-gray-400 hover:text-white">← رجوع</a>
        <h1 class="text-2xl font-bold">🚀 نشر إعلان جديد</h1>
    </div>

    {{-- Success --}}
    <div x-show="success" class="mb-4 p-4 rounded-lg" style="background:rgba(34,197,94,0.1);color:#4ade80;border:1px solid #22c55e">
        ✅ تم إرسال الإعلان لـ Meta بنجاح! سيبدأ الظهور خلال دقائق.
    </div>

    {{-- Error --}}
    <div x-show="error" class="mb-4 p-4 rounded-lg" style="background:rgba(239,68,68,0.1);color:#f87171;border:1px solid #ef4444">
        ❌ <span x-text="error"></span>
    </div>

    <div class="space-y-4">

        {{-- Step 1: المحتوى --}}
        <div class="card">
            <h2 class="text-lg font-bold mb-4">1️⃣ محتوى الإعلان</h2>

            <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-2">اسم الإعلان (داخلي)</label>
                <input type="text" x-model="form.name" placeholder="إعلان الشيف - مارينا دبي">
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-2">نص الإعلان الرئيسي</label>
                <textarea x-model="form.body" rows="3" placeholder="أشهى الوجبات الجاهزة توصل لبابك 🍽️ طعام طازج يومياً من شيف محترف"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-2">العنوان (Headline)</label>
                <input type="text" x-model="form.headline" placeholder="وجبات شيف جاهزة في دبي">
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-2">رابط الواتساب أو الموقع</label>
                <input type="url" x-model="form.link_url" placeholder="https://wa.me/971XXXXXXXXX">
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-2">صورة أو فيديو الإعلان</label>
                <div class="border-2 border-dashed rounded-lg p-6 text-center" 
                     style="border-color:#d4a017"
                     @dragover.prevent
                     @drop.prevent="handleFile($event.dataTransfer.files[0])">
                    <div x-show="!preview">
                        <p class="text-gray-400 mb-2">اسحب الملف هنا أو</p>
                        <label class="btn-primary cursor-pointer">
                            اختر ملف
                            <input type="file" class="hidden" accept="image/*,video/*" @change="handleFile($event.target.files[0])">
                        </label>
                        <p class="text-xs text-gray-500 mt-2">صورة JPG/PNG أو فيديو MP4 (حد أقصى 100MB)</p>
                    </div>
                    <div x-show="preview" class="relative">
                        <img x-show="fileType === 'image'" :src="preview" class="max-h-48 mx-auto rounded-lg">
                        <video x-show="fileType === 'video'" :src="preview" class="max-h-48 mx-auto rounded-lg" controls></video>
                        <button @click="preview = null; form.image_url = null" class="mt-2 text-red-400 text-sm">× إزالة</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 2: الجمهور --}}
        <div class="card">
            <h2 class="text-lg font-bold mb-4">2️⃣ الجمهور والمنطقة</h2>

            <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-2">الدولة</label>
                <select x-model="form.country">
                    <option value="AE">الإمارات العربية المتحدة 🇦🇪</option>
                    <option value="SA">المملكة العربية السعودية 🇸🇦</option>
                    <option value="LB">لبنان 🇱🇧</option>
                    <option value="KW">الكويت 🇰🇼</option>
                    <option value="QA">قطر 🇶🇦</option>
                    <option value="BH">البحرين 🇧🇭</option>
                    <option value="OM">عمان 🇴🇲</option>
                    <option value="JO">الأردن 🇯🇴</option>
                    <option value="EG">مصر 🇪🇬</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-2">المدينة (اختياري)</label>
                <input type="text" x-model="form.city" placeholder="دبي - مارينا">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">العمر من</label>
                    <select x-model="form.age_min">
                        <option value="18">18</option>
                        <option value="25" selected>25</option>
                        <option value="30">30</option>
                        <option value="35">35</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">العمر حتى</label>
                    <select x-model="form.age_max">
                        <option value="35">35</option>
                        <option value="45" selected>45</option>
                        <option value="55">55</option>
                        <option value="65">65+</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-2">الجنس</label>
                <select x-model="form.gender">
                    <option value="all">الكل</option>
                    <option value="male">رجال فقط</option>
                    <option value="female">نساء فقط</option>
                </select>
            </div>
        </div>

        {{-- Step 3: الميزانية --}}
        <div class="card">
            <h2 class="text-lg font-bold mb-4">3️⃣ الميزانية والمدة</h2>

            <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-2">الميزانية اليومية ($)</label>
                <input type="number" x-model="form.daily_budget" placeholder="5" min="1" max="10000">
                <p class="text-xs text-gray-500 mt-1">
                    الحد الأدنى $1/يوم — 
                    <span x-text="'بـ $' + (form.daily_budget || 0) + '/يوم ستحصل على تقريباً ' + Math.round((form.daily_budget || 0) / 5 * 1000) + ' – ' + Math.round((form.daily_budget || 0) / 2.7 * 1000) + ' ظهور يومياً'"></span>
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">تاريخ البداية</label>
                    <input type="date" x-model="form.start_date">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">تاريخ النهاية (اختياري)</label>
                    <input type="date" x-model="form.end_date">
                </div>
            </div>

            {{-- Summary --}}
            <div class="p-4 rounded-lg mt-4" style="background:#2d2d4e">
                <h3 class="font-bold mb-2 gold">ملخص الإعلان</h3>
                <div class="text-sm space-y-1 text-gray-300">
                    <p>📍 المنطقة: <span class="text-white" x-text="form.city || form.country"></span></p>
                    <p>💰 الميزانية: <span class="text-white" x-text="'$' + (form.daily_budget || 0) + ' / يوم'"></span></p>
                    <p>👁️ الظهورات المتوقعة: <span class="gold" x-text="Math.round((form.daily_budget || 0) / 5 * 1000) + ' – ' + Math.round((form.daily_budget || 0) / 2.7 * 1000) + ' يومياً'"></span></p>
                    <p>📅 المدة: <span class="text-white" x-text="form.start_date ? form.start_date + (form.end_date ? ' → ' + form.end_date : ' (مستمر)') : 'لم تحدد'"></span></p>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <button @click="launch()" :disabled="loading" class="btn-primary w-full text-lg py-4">
            <span x-show="!loading">🚀 نشر الإعلان الآن</span>
            <span x-show="loading">⏳ جاري الإرسال لـ Meta...</span>
        </button>

    </div>
</div>

<script>
function launchCampaign() {
    return {
        form: {
            name: '',
            body: '',
            headline: '',
            link_url: '',
            country: 'AE',
            city: '',
            age_min: '25',
            age_max: '45',
            gender: 'all',
            daily_budget: 5,
            start_date: new Date().toISOString().split('T')[0],
            end_date: '',
            image_url: null,
        },
        preview: null,
        fileType: null,
        loading: false,
        success: false,
        error: '',

        handleFile(file) {
            if (!file) return;
            this.fileType = file.type.startsWith('video') ? 'video' : 'image';
            this.preview = URL.createObjectURL(file);
            this.form.image_url = this.preview;
        },

        async launch() {
            if (!this.form.name || !this.form.body || !this.form.daily_budget) {
                this.error = 'يرجى ملء اسم الإعلان والنص والميزانية';
                return;
            }
            this.loading = true;
            this.error = '';

            try {
                const campaignRes = await api('POST', '/campaigns', {
                    name: this.form.name,
                    objective: 'AWARENESS',
                    budget_type: 'daily',
                    budget: this.form.daily_budget,
                    start_date: this.form.start_date,
                    end_date: this.form.end_date || null,
                });

                if (!campaignRes.campaign) {
                    this.error = campaignRes.message || 'فشل إنشاء الحملة';
                    this.loading = false;
                    return;
                }

                const campaignId = campaignRes.campaign.id;

                const adSetRes = await api('POST', `/campaigns/${campaignId}/ad-sets`, {
                    name: this.form.name + ' - مجموعة',
                    daily_budget: this.form.daily_budget,
                    targeting: {
                        geo_locations: {
                            countries: [this.form.country],
                            cities: this.form.city ? [{ name: this.form.city }] : [],
                        },
                        age_min: parseInt(this.form.age_min),
                        age_max: parseInt(this.form.age_max),
                        genders: this.form.gender === 'all' ? [1, 2] : this.form.gender === 'male' ? [1] : [2],
                    },
                    optimization_goal: 'REACH',
                    billing_event: 'IMPRESSIONS',
                });

                if (!adSetRes.ad_set) {
                    this.error = adSetRes.message || 'فشل إنشاء المجموعة';
                    this.loading = false;
                    return;
                }

                const adSetId = adSetRes.ad_set.id;

                const adRes = await api('POST', `/campaigns/${campaignId}/ad-sets/${adSetId}/ads`, {
                    name: this.form.name + ' - إعلان',
                    format: 'image',
                    headline: this.form.headline,
                    body: this.form.body,
                    link_url: this.form.link_url,
                    image_url: this.form.image_url,
                    call_to_action: 'LEARN_MORE',
                });

                if (adRes.ad) {
                    this.success = true;
                    window.scrollTo(0, 0);
                } else {
                    this.error = adRes.message || 'فشل إنشاء الإعلان';
                }

            } catch (e) {
                this.error = 'حدث خطأ: ' + e.message;
            }

            this.loading = false;
        }
    }
}
</script>
@endsection
