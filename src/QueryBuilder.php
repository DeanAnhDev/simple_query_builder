<?php
namespace QueryBuilder;

use PDO;
use PDOException;

class QueryBuilder {
    protected PDO $pdo;
    protected string $table = '';
    protected array $fields = ['*'];
    protected array $wheres = [];
    protected array $bindings = [];
    protected string $type = 'select';

    protected array $insertData = [];
    protected array $updateData = [];

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function table(string $table): self {
        $this->table = $table;
        return $this;
    }

    public function select(array $fields = ['*']): self {
        $this->fields = $fields;
        $this->type = 'select';
        return $this;
    }

    public function where(string $field, string $operator, $value): self {
        $param = ':' . $field . count($this->bindings);
        $this->wheres[] = "$field $operator $param";
        $this->bindings[$param] = $value;
        return $this;
    }

    public function insert(array $data): bool {
        $this->type = 'insert';
        $this->insertData = $data;

        $fields = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO $this->table ($fields) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function update(array $data): bool {
        $this->type = 'update';
        $this->updateData = $data;

        $setParts = [];
        foreach ($data as $field => $value) {
            $param = ':' . $field . '_upd';
            $setParts[] = "$field = $param";
            $this->bindings[$param] = $value;
        }

        $sql = "UPDATE $this->table SET " . implode(', ', $setParts);
        if ($this->wheres) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($this->bindings);
    }

    public function delete(): bool {
        $sql = "DELETE FROM $this->table";
        if ($this->wheres) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($this->bindings);
    }

    public function get(): array {
        $sql = "SELECT " . implode(', ', $this->fields) . " FROM $this->table";

        if ($this->wheres) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
