<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex items-center justify-center min-h-screen" style="background:#0f0f1a">

<div class="w-full max-w-md" x-data="registerForm()">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold gold">🎯 منصة الإعلانات</h1>
        <p class="text-gray-400 mt-2">ابدأ تجربتك المجانية 14 يوم</p>
    </div>

    <div class="card">
        <h2 class="text-xl font-bold mb-6">إنشاء حساب جديد</h2>

        <div x-show="error" class="mb-4 p-3 rounded-lg text-sm" style="background:rgba(239,68,68,0.1); color:#f87171; border:1px solid #ef4444">
            <span x-text="error"></span>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1">الاسم الكامل</label>
                <input type="text" x-model="form.name" placeholder="محمد أحمد">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">اسم الشركة</label>
                <input type="text" x-model="form.company_name" placeholder="شركة النجاح">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">البريد الإلكتروني</label>
                <input type="email" x-model="form.email" placeholder="example@email.com">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">كلمة المرور</label>
                <input type="password" x-model="form.password" placeholder="8 أحرف على الأقل">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">تأكيد كلمة المرور</label>
                <input type="password" x-model="form.password_confirmation" placeholder="••••••••">
            </div>
            <button @click="submit()" :disabled="loading" class="btn-primary w-full">
                <span x-show="!loading">إنشاء الحساب</span>
                <span x-show="loading">جاري الإنشاء...</span>
            </button>
        </div>

        <p class="text-center text-sm text-gray-400 mt-4">
            لديك حساب؟
            <a href="/login" class="gold hover:underline">تسجيل الدخول</a>
        </p>
    </div>
</div>

<script>
function registerForm() {
    return {
        form: { name: '', company_name: '', email: '', password: '', password_confirmation: '' },
        error: '',
        loading: false,
        async submit() {
            this.loading = true;
            this.error = '';
            const res = await api('POST', '/auth/register', this.form);
            if (res.token) {
                localStorage.setItem('token', res.token);
                window.location.href = '/dashboard';
            } else {
                this.error = res.message || 'حدث خطأ';
            }
            this.loading = false;
        }
    }
}
</script>
</body>
</html>
