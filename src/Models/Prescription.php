<?php

namespace App\Models;

use App\Core\Model;

class Prescription extends Model
{
    protected string $table = 'prescriptions';
    protected array $fillable = ['patient_id', 'doctor_id', 'medicines', 'notes'];
    protected array $hidden = [];

    public function createPrescription(int $patientId, int $doctorId, string $medicines, string $notes = ''): int|false
    {
        return $this->create([
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'medicines' => $medicines,
            'notes' => $notes,
        ]);
    }

    public function getByPatient(int $patientId): array
    {
        return $this->fetchAll("
            SELECT p.*, u.username as doctor_name
            FROM {$this->table} p
            LEFT JOIN users u ON p.doctor_id = u.id
            WHERE p.patient_id = ?
            ORDER BY p.created_at DESC
        ", [$patientId]);
    }

    public function getByDoctor(int $doctorId): array
    {
        return $this->fetchAll("
            SELECT p.*, pt.name as patient_name
            FROM {$this->table} p
            LEFT JOIN patients pt ON p.patient_id = pt.id
            WHERE p.doctor_id = ?
            ORDER BY p.created_at DESC
        ", [$doctorId]);
    }
}