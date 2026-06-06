import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

window.API_BASE = '/api/v1';

window.api = async (method, url, data = null) => {
    const token = localStorage.getItem('token');
    const opts = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
        },
    };
    if (data) opts.body = JSON.stringify(data);
    const res = await fetch(API_BASE + url, opts);
    return res.json();
};
