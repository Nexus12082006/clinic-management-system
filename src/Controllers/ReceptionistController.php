<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;
use App\Services\PatientService;
use App\Services\AppointmentService;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\User;

class ReceptionistController extends Controller
{
    private PatientService $patientService;
    private AppointmentService $appointmentService;

    public function __construct(Request $request, Session $session, Auth $auth, Database $db)
    {
        parent::__construct($request, $session, $auth, $db);
        $this->patientService = new PatientService(new Patient($db));
        $this->appointmentService = new AppointmentService(new Appointment($db));
    }

    public function dashboard(): \App\Core\Response
    {
        $patientCount = count($this->patientService->getAllPatients());
        $appointmentCount = count($this->appointmentService->getAllAppointments());
        $recentAppointments = $this->appointmentService->getRecentAppointments(5);
        
        $doctorModel = new User($this->db);
        $doctors = $doctorModel->getByRole('Doctor');
        
        $medicineCount = $this->db->fetchColumn("SELECT COUNT(*) FROM medicines") ?? 0;

        $this->with('patientCount', $patientCount)
             ->with('appointmentCount', $appointmentCount)
             ->with('appointments', $recentAppointments)
             ->with('doctors', $doctors)
             ->with('medicineCount', (int)$medicineCount);

        return $this->view('receptionist.dashboard');
    }

    public function createPatient(): \App\Core\Response
    {
        return $this->view('receptionist.create_patient');
    }

    public function storePatient(): \App\Core\Response
    {
        $data = $this->validate([
            'name' => 'required|string|min:2|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'required|string|min:10|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $patientId = $this->patientService->createPatient(
            $data['name'],
            $data['email'] ?? '',
            $data['phone'],
            $data['address'] ?? ''
        );

        if ($patientId) {
            $this->flash('success', 'Patient registered successfully.');
        } else {
            $this->flash('error', 'Failed to register patient.');
        }

        return $this->redirect(config('app.url') . '/receptionist');
    }

    public function createAppointment(): \App\Core\Response
    {
        $patients = $this->patientService->getAllPatients();
        $this->with('patients', $patients);
        return $this->view('receptionist.create_appointment');
    }

    public function storeAppointment(): \App\Core\Response
    {
        $data = $this->validate([
            'patient_id' => 'required|integer',
            'appointment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $appointmentId = $this->appointmentService->scheduleAppointment(
            $data['patient_id'],
            $data['appointment_date'],
            $data['notes'] ?? ''
        );

        if ($appointmentId) {
            $this->flash('success', 'Appointment scheduled successfully.');
        } else {
            $this->flash('error', 'Failed to schedule appointment.');
        }

        return $this->redirect(config('app.url') . '/receptionist');
    }

    public function completeAppointment(int $id): \App\Core\Response
    {
        if ($this->appointmentService->completeAppointment($id)) {
            $this->flash('success', 'Appointment marked as completed.');
        } else {
            $this->flash('error', 'Failed to complete appointment.');
        }

        return $this->redirect(config('app.url') . '/receptionist');
    }

    public function deletePatient(int $id): \App\Core\Response
    {
        if ($this->patientService->deletePatient($id)) {
            $this->flash('success', 'Patient deleted successfully.');
        } else {
            $this->flash('error', 'Failed to delete patient.');
        }

        return $this->redirect(config('app.url') . '/receptionist/patients');
    }
}