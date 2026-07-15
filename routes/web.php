<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 博客首页路由，由 HomeController 的 index 方法处理
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
