<?php

namespace App\Models;

use App\Core\Model;

class Patient extends Model
{
    protected string $table = 'patients';
    protected array $fillable = ['name', 'email', 'phone', 'address'];
    protected array $hidden = [];

    public function getAll(): array
    {
        return $this->fetchAll("SELECT * FROM {$this->table} ORDER BY name ASC");
    }

    public function search(string $query): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE name LIKE ? OR phone LIKE ? ORDER BY name ASC";
        $param = "%{$query}%";
        return $this->fetchAll($sql, [$param, $param]);
    }

    public function getWithAppointmentCount(): array
    {
        return $this->fetchAll("
            SELECT p.*, COUNT(a.id) as appointment_count
            FROM {$this->table} p
            LEFT JOIN appointments a ON p.id = a.patient_id
            GROUP BY p.id
            ORDER BY p.name ASC
        ");
    }
}