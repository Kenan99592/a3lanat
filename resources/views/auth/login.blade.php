<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex items-center justify-center min-h-screen" style="background:#0f0f1a">

<div class="w-full max-w-md" x-data="loginForm()">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold gold">🎯 منصة الإعلانات</h1>
        <p class="text-gray-400 mt-2">إدارة إعلانات Meta باحترافية</p>
    </div>

    <div class="card">
        <h2 class="text-xl font-bold mb-6">تسجيل الدخول</h2>

        <div x-show="error" class="mb-4 p-3 rounded-lg text-sm" style="background:rgba(239,68,68,0.1); color:#f87171; border:1px solid #ef4444">
            <span x-text="error"></span>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1">البريد الإلكتروني</label>
                <input type="email" x-model="form.email" placeholder="example@email.com">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">كلمة المرور</label>
                <input type="password" x-model="form.password" placeholder="••••••••">
            </div>
            <button @click="submit()" :disabled="loading" class="btn-primary w-full">
                <span x-show="!loading">دخول</span>
                <span x-show="loading">جاري التحقق...</span>
            </button>
        </div>

        <p class="text-center text-sm text-gray-400 mt-4">
            ليس لديك حساب؟
            <a href="/register" class="gold hover:underline">إنشاء حساب</a>
        </p>
    </div>
</div>

<script>
function loginForm() {
    return {
        form: { email: '', password: '' },
        error: '',
        loading: false,
        async submit() {
            this.loading = true;
            this.error = '';
            const res = await api('POST', '/auth/login', this.form);
            if (res.token) {
                localStorage.setItem('token', res.token);
                window.location.href = '/dashboard';
            } else {
                this.error = res.message || 'بيانات غير صحيحة';
            }
            this.loading = false;
        }
    }
}
</script>
</body>
</html>
