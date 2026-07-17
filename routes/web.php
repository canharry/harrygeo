<?php

use App\Http\Controllers\Admin\LanguageController;
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

// 文章列表页路由
Route::get('/posts', [App\Http\Controllers\PostController::class, 'index'])->name('posts.index');

// 文章详情页路由
Route::get('/posts/{slug}', [App\Http\Controllers\PostController::class, 'show'])->name('posts.show');

// 文章点赞路由
Route::post('/posts/{slug}/like', [App\Http\Controllers\PostController::class, 'like'])->name('posts.like');

// 分类归档页路由
Route::get('/categories/{slug}', [App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');

// 标签归档页路由
Route::get('/tags/{slug}', [App\Http\Controllers\TagController::class, 'show'])->name('tags.show');

// 前台用户认证路由
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
});

Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// 个人资料（登录后可访问）
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

// 后台语言切换路由
Route::post('/admin/language/{locale}', [LanguageController::class, 'switch'])
    ->name('admin.language.switch')
    ->whereIn('locale', ['zh_CN', 'en']);
