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
Usage Example
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

// Select users có status = 'active'
$users = $qb->table('users')
    ->select(['id', 'name', 'email', 'status'])
    ->where('status', '=', 'active')
    ->get();

echo "Danh sách user active:\n";
print_r($users);


```

Config:
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

| Phương thức                  | Mô tả chức năng                                             |
| ---------------------------- | ----------------------------------------------------------- |
| `table($name)`               | Chọn bảng muốn thao tác                                     |
| `select([$cols])`            | Chọn các cột cần lấy dữ liệu (mặc định là `*` – tất cả cột) |
| `where($field, $op, $value)` | Thêm điều kiện WHERE                                        |
| `insert($data)`              | Thêm dữ liệu mới vào bảng                                   |
| `update($data)`              | Cập nhật dữ liệu dựa theo điều kiện đã đặt (`where`)        |
| `delete()`                   | Xoá dữ liệu theo điều kiện đã đặt (`where`)                 |
| `get()`                      | Thực thi câu lệnh SELECT và trả về kết quả                  |
