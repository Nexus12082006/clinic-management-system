<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\MedicalRecord;

class PatientController extends Controller
{
    public function __construct(Request $request, Session $session, Auth $auth, Database $db)
    {
        parent::__construct($request, $session, $auth, $db);
    }

    public function dashboard(): \App\Core\Response
    {
        $username = $this->auth->user()->username;
        
        $patientModel = new Patient($this->db);
        $patient = $patientModel->findBy('name', $username);
        $patientId = $patient['id'] ?? 0;

        $appointmentModel = new Appointment($this->db);
        $appointments = $appointmentModel->getByPatient($patientId);

        $prescriptionModel = new Prescription($this->db);
        $prescriptions = $prescriptionModel->getByPatient($patientId);

        $recordModel = new MedicalRecord($this->db);
        $records = $recordModel->getByPatient($patientId);

        $this->with('patient', $patient)
             ->with('appointments', $appointments)
             ->with('prescriptions', $prescriptions)
             ->with('records', $records);

        return $this->view('patient.dashboard');
    }

    public function appointments(): \App\Core\Response
    {
        $username = $this->auth->user()->username;
        $patientModel = new Patient($this->db);
        $patient = $patientModel->findBy('name', $username);
        $patientId = $patient['id'] ?? 0;

        $appointmentModel = new Appointment($this->db);
        $appointments = $appointmentModel->getByPatient($patientId);

        $this->with('appointments', $appointments);
        return $this->view('patient.appointments');
    }

    public function records(): \App\Core\Response
    {
        $username = $this->auth->user()->username;
        $patientModel = new Patient($this->db);
        $patient = $patientModel->findBy('name', $username);
        $patientId = $patient['id'] ?? 0;

        $recordModel = new MedicalRecord($this->db);
        $records = $recordModel->getByPatient($patientId);

        $this->with('records', $records);
        return $this->view('patient.records');
    }

    public function prescriptions(): \App\Core\Response
    {
        $username = $this->auth->user()->username;
        $patientModel = new Patient($this->db);
        $patient = $patientModel->findBy('name', $username);
        $patientId = $patient['id'] ?? 0;

        $prescriptionModel = new Prescription($this->db);
        $prescriptions = $prescriptionModel->getByPatient($patientId);

        $this->with('prescriptions', $prescriptions);
        return $this->view('patient.prescriptions');
    }
}