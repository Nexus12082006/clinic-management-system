<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;
use App\Services\PatientService;
use App\Services\AppointmentService;
use App\Services\NotificationService;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;

class DoctorController extends Controller
{
    private PatientService $patientService;
    private AppointmentService $appointmentService;
    private NotificationService $notificationService;

    public function __construct(Request $request, Session $session, Auth $auth, Database $db)
    {
        parent::__construct($request, $session, $auth, $db);
        $this->patientService = new PatientService(new Patient($db));
        $this->appointmentService = new AppointmentService(new Appointment($db));
        $this->notificationService = new NotificationService();
    }

    public function dashboard(): \App\Core\Response
    {
        $patientCount = $this->patientService->getAllPatients();
        $appointmentCount = $this->appointmentService->getAllAppointments();
        $recentAppointments = $this->appointmentService->getRecentAppointments(5);
        
        $recordModel = new MedicalRecord($this->db);
        $recordCount = $recordModel->fetchColumn("SELECT COUNT(*) FROM medical_records");

        $this->with('patientCount', count($patientCount))
             ->with('appointmentCount', count($appointmentCount))
             ->with('recordCount', (int)$recordCount)
             ->with('appointments', $recentAppointments);

        return $this->view('doctor.dashboard');
    }

    public function appointments(): \App\Core\Response
    {
        $appointments = $this->appointmentService->getPendingAppointments();
        $this->with('appointments', $appointments);
        return $this->view('doctor.appointments');
    }

    public function completeAppointment(int $id): \App\Core\Response
    {
        if ($this->appointmentService->completeAppointment($id)) {
            $this->flash('success', 'Appointment marked as completed.');
        } else {
            $this->flash('error', 'Failed to complete appointment.');
        }

        return $this->redirect(config('app.url') . '/doctor');
    }

    public function createPrescription(): \App\Core\Response
    {
        $patients = $this->patientService->getAllPatients();
        $this->with('patients', $patients);
        return $this->view('doctor.create_prescription');
    }

    public function storePrescription(): \App\Core\Response
    {
        $data = $this->validate([
            'patient_id' => 'required|integer',
            'medicines' => 'required|string|min:1|max:2000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $doctorId = $this->auth->id();
        $prescriptionModel = new Prescription($this->db);
        
        if ($prescriptionModel->createPrescription(
            $data['patient_id'],
            $doctorId,
            $data['medicines'],
            $data['notes'] ?? ''
        )) {
            $this->flash('success', 'Prescription issued successfully.');
            $this->notificationService->sendPrescriptionNotification($data['patient_id']);
        } else {
            $this->flash('error', 'Failed to issue prescription.');
        }

        return $this->redirect(config('app.url') . '/doctor');
    }

    public function createRecord(): \App\Core\Response
    {
        $patients = $this->patientService->getAllPatients();
        $this->with('patients', $patients);
        return $this->view('doctor.create_record');
    }

    public function storeRecord(): \App\Core\Response
    {
        $data = $this->validate([
            'patient_id' => 'required|integer',
            'diagnosis' => 'required|string|min:1|max:2000',
            'treatment_notes' => 'nullable|string|max:2000',
            'medication_list' => 'nullable|string|max:2000',
            'dosage_duration' => 'nullable|string|max:500',
        ]);

        $doctorId = $this->auth->id();
        $recordModel = new MedicalRecord($this->db);
        $prescriptionModel = new Prescription($this->db);

        $recordId = $recordModel->createRecord(
            $data['patient_id'],
            $doctorId,
            $data['diagnosis'],
            $data['treatment_notes'] ?? ''
        );

        if ($recordId && !empty($data['medication_list'])) {
            $prescriptionModel->createPrescription(
                $data['patient_id'],
                $doctorId,
                $data['medication_list'],
                $data['dosage_duration'] ?? ''
            );
        }

        if ($recordId) {
            $this->flash('success', 'Medical record saved successfully.');
        } else {
            $this->flash('error', 'Failed to save medical record.');
        }

        return $this->redirect(config('app.url') . '/doctor');
    }
}