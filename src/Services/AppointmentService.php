<?php

namespace App\Services;

use App\Models\Appointment;

class AppointmentService
{
    private Appointment $appointmentModel;

    public function __construct(Appointment $appointmentModel)
    {
        $this->appointmentModel = $appointmentModel;
    }

    public function getAllAppointments(): array
    {
        return $this->appointmentModel->getAll();
    }

    public function getAppointmentsByPatient(int $patientId): array
    {
        return $this->appointmentModel->getByPatient($patientId);
    }

    public function getPendingAppointments(): array
    {
        return $this->appointmentModel->getPending();
    }

    public function getRecentAppointments(int $limit = 5): array
    {
        return $this->appointmentModel->getRecent($limit);
    }

    public function scheduleAppointment(int $patientId, string $dateTime, string $notes = ''): int|false
    {
        if (empty($patientId) || empty($dateTime)) {
            return false;
        }

        return $this->appointmentModel->create([
            'patient_id' => $patientId,
            'appointment_date' => $dateTime,
            'status' => 'Pending',
            'notes' => $notes,
        ]);
    }

    public function completeAppointment(int $id): bool
    {
        return $this->appointmentModel->complete($id) > 0;
    }

    public function deleteAppointment(int $id): bool
    {
        return $this->appointmentModel->delete($id) > 0;
    }

    public function getStats(): array
    {
        return $this->appointmentModel->getStats();
    }
}