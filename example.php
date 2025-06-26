<?php

require_once __DIR__ . '/vendor/autoload.php';

use QueryBuilder\QueryBuilder;

// Load cấu hình từ file config
$config = require __DIR__ . '/config/database.php';

$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";


$pdo = new PDO($dsn, $config['username'], $config['password']);

// Khởi tạo QueryBuilder
$db = new QueryBuilder($pdo);

$id = $db->table('users')->insert([
    'name' => 'Alice',
    'email' => 'alice1@example.com',
]);
echo "Inserted ID: $id\n";

$users = $db->table('users')->select()->get();
print_r($users);

$user = $db->table('users')
    ->where('name', '=', 'Alice')
    ->first();
print_r($user);

$count = $db->table('users')->count();
echo "User count: $count\n";

$names = $db->table('users')->pluck('name');
print_r($names);

$users = $db->table('users')
    ->orderBy('id', 'desc')
    ->limit(2)
    ->get();
print_r($users);


$results = $db->table('users')
    ->select(['name', 'COUNT(*) as total'])
    ->groupBy('name')
    ->having('total', '>', 0)
    ->get();
print_r($results);

$joined = $db->table('users')
    ->select(['users.name', 'orders.product_name'])
    ->join('orders', 'users.id', '=', 'orders.user_id')
    ->get();
print_r($joined);
