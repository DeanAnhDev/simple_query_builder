<?php

require_once __DIR__ . '/vendor/autoload.php';

use QueryBuilder\QueryBuilder;

// Load cấu hình từ file config
$config = require __DIR__ . '/config/database.php';

$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, $config['username'], $config['password'], $options);

// Khởi tạo QueryBuilder
$qb = new QueryBuilder($pdo);

// ✅ Insert user
$qb->table('users')->insert([
    'name' => 'Alice',
    'email' => 'alice@example.com',
    'password' => password_hash('secret123', PASSWORD_DEFAULT),
    'status' => 'active',
]);

// ✅ Select users có status = 'active'
$users = $qb->table('users')
    ->select(['id', 'name', 'email', 'status'])
    ->where('status', '=', 'active')
    ->get();

echo "Danh sách user active:\n";
print_r($users);

// Update user có id = 1
//$qb->table('users')
//    ->where('id', '=', 1)
//    ->update(['email' => 'alice.updated@example.com']);

// Delete user có id = 2
//$qb->table('users')
//    ->where('id', '=', 2)
//    ->delete();
