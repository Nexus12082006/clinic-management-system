<?php

namespace App\Services;

use App\Models\Patient;

class PatientService
{
    private Patient $patientModel;

    public function __construct(Patient $patientModel)
    {
        $this->patientModel = $patientModel;
    }

    public function getAllPatients(): array
    {
        return $this->patientModel->getAll();
    }

    public function searchPatients(string $query): array
    {
        return $this->patientModel->search($query);
    }

    public function getPatientsWithAppointmentCount(): array
    {
        return $this->patientModel->getWithAppointmentCount();
    }

    public function createPatient(string $name, string $email, string $phone, string $address): int|false
    {
        if (empty($name) || empty($phone)) {
            return false;
        }

        return $this->patientModel->create([
            'name' => $name,
            'email' => $email ?: '',
            'phone' => $phone,
            'address' => $address ?: '',
        ]);
    }

    public function getPatient(int $id): ?array
    {
        return $this->patientModel->find($id);
    }

    public function deletePatient(int $id): bool
    {
        return $this->patientModel->delete($id) > 0;
    }
}