# 🔧 Simple PHP Query Builder

Một thư viện PHP nhẹ, không phụ thuộc, giúp xây dựng câu lệnh SQL bằng cách xâu chuỗi phương thức. Hoạt động tốt với mọi cơ sở dữ liệu hỗ trợ PDO.

## 🚀 Features

Dễ dàng sử dụng với cú pháp xâu chuỗi phương thức: select(), where(), insert(), update(), delete()

Hỗ trợ prepared statement và binding tham số an toàn

Cú pháp rõ ràng, lấy cảm hứng từ Laravel Query Builder

Không phụ thuộc bên ngoài, chỉ dùng PHP thuần

## 📦 Installation

```bash
composer require theanh/query-builder


```
Usage Example
```php
use QueryBuilder\QueryBuilder;

$config = require 'config/database.php';
$pdo = new PDO($config['dsn'], $config['username'], $config['password']);

$qb = new QueryBuilder($pdo);

// SELECT
$users = $qb->table('users')
            ->select(['id', 'name'])
            ->where('status', '=', 'active')
            ->get();

// INSERT
$qb->table('users')->insert([
    'name' => 'Alice',
    'email' => 'alice@example.com'
]);

// UPDATE
$qb->table('users')
   ->where('id', '=', 1)
   ->update(['name' => 'Bob']);

// DELETE
$qb->table('users')
   ->where('id', '=', 2)
   ->delete();

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
