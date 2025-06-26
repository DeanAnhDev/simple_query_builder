# 🔧 Simple PHP Query Builder

Một thư viện PHP nhẹ, không phụ thuộc, giúp xây dựng câu lệnh SQL bằng cách xâu chuỗi phương thức. Hoạt động tốt với mọi cơ sở dữ liệu hỗ trợ PDO.

## 🚀 Features

Dễ dàng sử dụng với cú pháp xâu chuỗi phương thức: select(), where(), insert(), update(), delete()

Hỗ trợ prepared statement và binding tham số an toàn

Cú pháp rõ ràng, lấy cảm hứng từ Laravel Query Builder

Không phụ thuộc bên ngoài, chỉ dùng PHP thuần

## 📦 Installation

```bash
composer require deananhdev/query-builder

```

### Yêu cầu: PHP >= 8.0


# Usage Example
```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use QueryBuilder\QueryBuilder;

// Load cấu hình từ file config
$config = require __DIR__ . '/config/database.php';

$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

$pdo = new PDO($dsn, $config['username'], $config['password']);

// Khởi tạo QueryBuilder
$qb = new QueryBuilder($pdo);

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


```

## Config:
```php
<?php
return [
    'host'     => 'localhost',
    'dbname'   => 'simple_query_builder',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8mb4'
];

```
Tham chiếu các phương thức (Class Reference)

| Phương thức                                               | Mô tả chức năng                                   |                        |
|-----------------------------------------------------------|---------------------------------------------------|------------------------|
| `table(string $name): self`                               | Chọn bảng muốn thao tác                           |                        |
| `select(array $cols = ['*']): self`                       | Chọn các cột cần lấy dữ liệu (mặc định là `*`)    |                        |
| `where(string $field, string $op, $val): self`            | Thêm điều kiện WHERE (hỗ trợ nhiều điều kiện nối bằng AND) |                        |
| `insert(array $data): bool`                               | Thêm dữ liệu mới vào bảng                         |                        |
| `update(array $data): bool`                               | Cập nhật dữ liệu dựa theo điều kiện đã đặt (`where`) |                        |
| `delete(): bool`                                          | Xoá dữ liệu theo điều kiện đã đặt (`where`)       |                        |
| `get(): array`                                            | Thực thi câu lệnh SELECT và trả về tất cả bản ghi |                        |
| `first(): ?array`                                         | Trả về bản ghi đầu tiên (giới hạn 1 kết quả)      |                        |
| `orderBy(string $col, string $dir): self`                 | Thêm điều kiện sắp xếp (mặc định `asc`)           |                        |
| `limit(int $number): self`                                | Giới hạn số lượng kết quả trả về                  |                        |
| `count(): int`                                            | Trả về tổng số bản ghi theo điều kiện hiện tại    |                        |
| `pluck(string $column): array`                            | Lấy danh sách giá trị của 1 cột                   |                        |
| `reset(): self`                                           | Reset lại trạng thái query builder (dùng để thực hiện truy vấn mới) |                        |
| `join($table, $first, $op, $second, $type='INNER'): self` | Thêm JOIN giữa các bảng                           |                        |
| `leftJoin(...)`                                           | Shortcut cho `join(..., 'LEFT')`                  |                        |
| `groupBy(string or array $columns): self`                 | Gom nhóm kết quả theo cột  |
| `having($field, $operator, $value): self`                 | Thêm điều kiện `HAVING` sau `GROUP BY`            |                        |
| `havingRaw(string $raw): self`                            | Thêm điều kiện HAVING theo câu lệnh SQL tùy chỉnh |                        |
