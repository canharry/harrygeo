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

// 文章搜索路由
Route::get('/search', [App\Http\Controllers\PostController::class, 'search'])->name('posts.search');

// 文章详情页路由
Route::get('/posts/{slug}', [App\Http\Controllers\PostController::class, 'show'])->name('posts.show');

// 文章点赞路由
Route::post('/posts/{slug}/like', [App\Http\Controllers\PostController::class, 'like'])->name('posts.like');

// 文章评论提交路由（需登录）
Route::post('/posts/{slug}/comments', [App\Http\Controllers\PostController::class, 'storeComment'])
    ->name('posts.comments.store')
    ->middleware('auth');

// 文章评论删除路由（需登录）
Route::delete('/posts/{slug}/comments/{comment}', [App\Http\Controllers\PostController::class, 'destroyComment'])
    ->name('posts.comments.destroy')
    ->middleware('auth');

// 文章评论修改路由（需登录）
Route::put('/posts/{slug}/comments/{comment}', [App\Http\Controllers\PostController::class, 'updateComment'])
    ->name('posts.comments.update')
    ->middleware('auth');

// 评论图片上传路由（需登录）
Route::post('/comments/upload-image', [App\Http\Controllers\PostController::class, 'uploadCommentImage'])
    ->name('comments.upload-image')
    ->middleware('auth');

// 正文视频上传路由（需登录）
Route::post('/posts/upload-video', [App\Http\Controllers\PostController::class, 'uploadContentVideo'])
    ->name('posts.upload-video')
    ->middleware('auth');

// 分类列表与归档页路由
Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{slug}', [App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');

// 标签列表与归档页路由
Route::get('/tags', [App\Http\Controllers\TagController::class, 'index'])->name('tags.index');
Route::get('/tags/{slug}', [App\Http\Controllers\TagController::class, 'show'])->name('tags.show');

// 用户（博主）的分类与标签归档页
Route::get('/users/{user}/categories', [App\Http\Controllers\UserArchiveController::class, 'categories'])->name('users.categories');
Route::get('/users/{user}/tags', [App\Http\Controllers\UserArchiveController::class, 'tags'])->name('users.tags');

// 前台用户认证路由
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);

    // 忘记密码 / 重置密码
    Route::get('/forgot-password', [App\Http\Controllers\AuthController::class, 'showForgotPasswordForm'])
        ->name('password.request');
    Route::post('/forgot-password', [App\Http\Controllers\AuthController::class, 'sendResetLinkEmail'])
        ->name('password.email')
        ->middleware('throttle:5,1');
    Route::get('/reset-password/{token}', [App\Http\Controllers\AuthController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\AuthController::class, 'reset'])
        ->name('password.update')
        ->middleware('throttle:5,1');
});

Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// 个人资料与消息中心（登录后可访问）
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // 消息中心
    Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
});

// 后台语言切换路由
Route::post('/admin/language/{locale}', [LanguageController::class, 'switch'])
    ->name('admin.language.switch')
    ->whereIn('locale', ['zh_CN', 'en']);
