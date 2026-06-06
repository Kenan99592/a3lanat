<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'منصة الإعلانات')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ sidebarOpen: true }">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-64 bg-dark-800 border-l border-gray-800 flex flex-col" style="background:#1a1a2e; border-right: 1px solid #2d2d4e;">
        <div class="p-6 border-b border-gray-800" style="border-color:#2d2d4e">
            <h1 class="text-xl font-bold gold">🎯 منصة الإعلانات</h1>
            <p class="text-xs text-gray-400 mt-1">إدارة إعلانات Meta</p>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            <a href="/dashboard" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}">
                <span>📊</span> لوحة التحكم
            </a>
            <a href="/campaigns" class="sidebar-link {{ request()->is('campaigns*') ? 'active' : '' }}">
                <span>📢</span> الحملات
            </a>
            <a href="/analytics" class="sidebar-link {{ request()->is('analytics*') ? 'active' : '' }}">
                <span>📈</span> التحليلات
            </a>
            <a href="/billing" class="sidebar-link {{ request()->is('billing*') ? 'active' : '' }}">
                <span>💳</span> الفوترة
            </a>
        </nav>

        <div class="p-4 border-t" style="border-color:#2d2d4e">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gold flex items-center justify-center text-dark-900 font-bold text-sm">ك</div>
                <div>
                    <p class="text-sm font-medium" id="user-name">المستخدم</p>
                    <p class="text-xs text-gray-400" id="user-email"></p>
                </div>
            </div>
            <button onclick="logout()" class="btn-secondary w-full mt-3 text-sm">تسجيل الخروج</button>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 overflow-auto" style="background:#0f0f1a">
        <div class="p-6">
            @yield('content')
        </div>
    </main>

</div>

<script>
async function logout() {
    await api('POST', '/auth/logout');
    localStorage.removeItem('token');
    window.location.href = '/login';
}

async function loadUser() {
    const token = localStorage.getItem('token');
    if (!token && !window.location.pathname.includes('login') && !window.location.pathname.includes('register')) {
        window.location.href = '/login';
        return;
    }
    if (token) {
        const res = await api('GET', '/auth/me');
        if (res.user) {
            document.getElementById('user-name').textContent = res.user.name;
            document.getElementById('user-email').textContent = res.user.email;
        }
    }
}
loadUser();
</script>

</body>
</html>
