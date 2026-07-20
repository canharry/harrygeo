<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$users = User::select('id', 'name', 'email', 'is_admin')->get();
echo "Total users: " . $users->count() . "\n";
foreach ($users as $user) {
    $name = $user->name ?: 'NULL';
    $email = $user->email ?: 'NULL';
    echo "ID={$user->id}, Name={$name}, Email={$email}, Admin=" . ($user->is_admin ? '1' : '0') . "\n";
}

$harry = User::where('name', 'harry')->first();
if ($harry) {
    echo "\nHarry found: ID={$harry->id}, Email={$harry->email}, Admin=" . ($harry->is_admin ? '1' : '0') . "\n";
}
