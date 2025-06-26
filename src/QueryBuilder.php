<?php
namespace QueryBuilder;

use PDO;
use PDOException;
use Exception;

class QueryBuilder {
    protected PDO $pdo;
    protected string $table = '';
    protected array $fields = ['*'];
    protected array $wheres = [];
    protected array $bindings = [];
    protected string $type = 'select';

    protected array $insertData = [];
    protected array $updateData = [];

    protected array $orderBys = [];
    protected ?int $limit = null;

    protected array $joins = [];
    protected array $groupBys = [];
    protected array $havings = [];

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
        $paramKey = str_replace('.', '_', $field) . count($this->bindings);
        $param = ':' . $paramKey;
        $this->wheres[] = "$field $operator $param";
        $this->bindings[$paramKey] = $value;
        return $this;
    }

    public function insert(array $data): int|false {
        $this->ensureTable();
        $this->type = 'insert';
        $this->insertData = $data;

        $fields = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);

        $success = $stmt->execute($data);
        $this->reset();
        return $success ? (int)$this->pdo->lastInsertId() : false;
    }

    public function update(array $data): bool {
        $this->ensureTable();
        $this->type = 'update';
        $this->updateData = $data;

        $setParts = [];
        foreach ($data as $field => $value) {
            $key = $field . '_upd';
            $param = ':' . $key;
            $setParts[] = "$field = $param";
            $this->bindings[$key] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts);
        $sql .= $this->buildWhereClause();

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($this->bindings);
        $this->reset();
        return $result;
    }

    public function delete(): bool {
        $this->ensureTable();
        $sql = "DELETE FROM {$this->table}" . $this->buildWhereClause();

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($this->bindings);
        $this->reset();
        return $result;
    }

    public function get(): array {
        $this->ensureTable();

        $sql = "SELECT " . implode(', ', $this->fields) . " FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        $sql .= $this->buildWhereClause();

        if (!empty($this->groupBys)) {
            $sql .= " GROUP BY " . implode(', ', $this->groupBys);
        }

        if (!empty($this->havings)) {
            $sql .= " HAVING " . implode(' AND ', $this->havings);
        }

        if (!empty($this->orderBys)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBys);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT " . $this->limit;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->reset();
        return $results;
    }

    public function first(): ?array {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function orderBy(string $column, string $direction = 'asc'): self {
        $this->orderBys[] = "$column " . strtoupper($direction);
        return $this;
    }

    public function limit(int $number): self {
        $this->limit = $number;
        return $this;
    }

    public function count(): int {
        $backup = $this->fields;
        $this->fields = ['COUNT(*) as aggregate'];
        $result = $this->get();
        $this->fields = $backup;
        return (int)($result[0]['aggregate'] ?? 0);
    }

    public function pluck(string $column): array {
        $this->fields = [$column];
        $results = $this->get();
        return array_column($results, $column);
    }

    public function reset(): self {
        $this->table = '';
        $this->fields = ['*'];
        $this->wheres = [];
        $this->bindings = [];
        $this->orderBys = [];
        $this->limit = null;
        $this->type = 'select';
        $this->insertData = [];
        $this->updateData = [];
        $this->joins = [];
        $this->groupBys = [];
        $this->havings = [];
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self {
        $this->joins[] = "$type JOIN $table ON $first $operator $second";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    public function groupBy(string|array $columns): self {
        $this->groupBys = array_merge($this->groupBys, (array)$columns);
        return $this;
    }

    public function having(string $field, string $operator, $value): self {
        $paramKey = 'having_' . str_replace('.', '_', $field) . count($this->bindings);
        $param = ':' . $paramKey;
        $this->havings[] = "$field $operator $param";
        $this->bindings[$paramKey] = $value;
        return $this;
    }

    public function havingRaw(string $raw): self {
        $this->havings[] = $raw;
        return $this;
    }

    protected function buildWhereClause(): string {
        return !empty($this->wheres) ? " WHERE " . implode(' AND ', $this->wheres) : '';
    }

    protected function ensureTable(): void {
        if (empty($this->table)) {
            throw new Exception("Table not specified.");
        }
    }

    public function debugSql(): string {
        $sql = "SELECT " . implode(', ', $this->fields) . " FROM {$this->table}";
        $sql .= $this->buildWhereClause();
        return $sql;
    }
}
