<?php

namespace App\Core;

abstract class Model
{
    protected Database $db;
    protected string $table;
    protected array $fillable = [];
    protected array $hidden = ['password', 'password_hash', 'remember_token'];

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function all(array $columns = ['*']): array
    {
        $cols = implode(', ', $columns);
        return $this->db->fetchAll("SELECT {$cols} FROM {$this->table} ORDER BY id DESC");
    }

    public function find(int $id, array $columns = ['*']): ?array
    {
        $cols = implode(', ', $columns);
        return $this->db->fetch("SELECT {$cols} FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function findBy(string $column, mixed $value, array $columns = ['*']): ?array
    {
        $cols = implode(', ', $columns);
        return $this->db->fetch("SELECT {$cols} FROM {$this->table} WHERE {$column} = ?", [$value]);
    }

    public function where(array $conditions, array $columns = ['*']): array
    {
        $cols = implode(', ', $columns);
        $where = [];
        $params = [];
        
        foreach ($conditions as $column => $value) {
            $where[] = "{$column} = ?";
            $params[] = $value;
        }
        
        $sql = "SELECT {$cols} FROM {$this->table} WHERE " . implode(' AND ', $where);
        return $this->db->fetchAll($sql, $params);
    }

    public function create(array $data): int|false
    {
        $filtered = $this->filterFillable($data);
        return $this->db->insert($this->table, $filtered);
    }

    public function update(int $id, array $data): int
    {
        $filtered = $this->filterFillable($data);
        return $this->db->update($this->table, $filtered, 'id = ?', [$id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete($this->table, 'id = ?', [$id]);
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        return $this->db->query($sql, $params);
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        return $this->db->fetch($sql, $params);
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->db->fetchAll($sql, $params);
    }

    public function fetchColumn(string $sql, array $params = []): mixed
    {
        return $this->db->fetchColumn($sql, $params);
    }

    public function transaction(callable $callback): mixed
    {
        return $this->db->transaction($callback);
    }

    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }

    protected function hide(array $data): array
    {
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }
        return $data;
    }
}