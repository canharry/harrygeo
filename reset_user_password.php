<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('email', 'admin@qq.com')->first();

if (!$user) {
    var_dump('User not found');
    exit(1);
}

$user->password = bcrypt('123456');
$user->save();

var_dump('Password reset for ' . $user->email);
