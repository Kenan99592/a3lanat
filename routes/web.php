<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/login'));
Route::get('/login', fn() => view('auth.login'));
Route::get('/register', fn() => view('auth.register'));
Route::get('/dashboard', fn() => view('dashboard.index'));
Route::get('/campaigns', fn() => view('campaigns.index'));
Route::get('/campaigns/create', fn() => view('campaigns.create'));
Route::get('/analytics', fn() => view('dashboard.index'));
Route::get('/billing', fn() => view('billing.index'));
