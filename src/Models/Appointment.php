<?php

namespace App\Models;

use App\Core\Model;

class Appointment extends Model
{
    protected string $table = 'appointments';
    protected array $fillable = ['patient_id', 'appointment_date', 'status', 'notes'];
    protected array $hidden = [];

    public function getAll(): array
    {
        return $this->fetchAll("
            SELECT a.*, p.name as patient_name, p.phone
            FROM {$this->table} a
            LEFT JOIN patients p ON a.patient_id = p.id
            ORDER BY a.appointment_date DESC
        ");
    }

    public function getByPatient(int $patientId): array
    {
        return $this->fetchAll("
            SELECT a.*, p.name as patient_name
            FROM {$this->table} a
            LEFT JOIN patients p ON a.patient_id = p.id
            WHERE a.patient_id = ?
            ORDER BY a.appointment_date DESC
        ", [$patientId]);
    }

    public function getPending(): array
    {
        return $this->fetchAll("
            SELECT a.*, p.name as patient_name
            FROM {$this->table} a
            LEFT JOIN patients p ON a.patient_id = p.id
            WHERE a.status != 'Completed'
            ORDER BY a.appointment_date ASC
        ");
    }

    public function getRecent(int $limit = 5): array
    {
        return $this->fetchAll("
            SELECT a.*, p.name as patient_name
            FROM {$this->table} a
            LEFT JOIN patients p ON a.patient_id = p.id
            ORDER BY a.id DESC
            LIMIT {$limit}
        ");
    }

    public function complete(int $id): int
    {
        return $this->update($id, ['status' => 'Completed']);
    }

    public function getStats(): array
    {
        $total = $this->fetchColumn("SELECT COUNT(*) FROM {$this->table}");
        $pending = $this->fetchColumn("SELECT COUNT(*) FROM {$this->table} WHERE status != 'Completed'");
        $completed = $this->fetchColumn("SELECT COUNT(*) FROM {$this->table} WHERE status = 'Completed'");
        
        return [
            'total' => (int)$total,
            'pending' => (int)$pending,
            'completed' => (int)$completed,
        ];
    }
}