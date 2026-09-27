<?php

namespace App\Models;

use App\Core\Model;

class MedicalRecord extends Model
{
    protected string $table = 'medical_records';
    protected array $fillable = ['patient_id', 'doctor_id', 'diagnosis', 'treatment_notes'];
    protected array $hidden = [];

    public function createRecord(int $patientId, int $doctorId, string $diagnosis, string $treatmentNotes): int|false
    {
        return $this->create([
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'diagnosis' => $diagnosis,
            'treatment_notes' => $treatmentNotes,
        ]);
    }

    public function getByPatient(int $patientId): array
    {
        return $this->fetchAll("
            SELECT mr.*, u.username as doctor_name
            FROM {$this->table} mr
            LEFT JOIN users u ON mr.doctor_id = u.id
            WHERE mr.patient_id = ?
            ORDER BY mr.created_at DESC
        ", [$patientId]);
    }

    public function getByDoctor(int $doctorId): array
    {
        return $this->fetchAll("
            SELECT mr.*, p.name as patient_name
            FROM {$this->table} mr
            LEFT JOIN patients p ON mr.patient_id = p.id
            WHERE mr.doctor_id = ?
            ORDER BY mr.created_at DESC
        ", [$doctorId]);
    }
}