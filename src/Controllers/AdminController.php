<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;
use App\Services\UserService;
use App\Services\PatientService;
use App\Services\AppointmentService;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;

class AdminController extends Controller
{
    private UserService $userService;
    private PatientService $patientService;
    private AppointmentService $appointmentService;

    public function __construct(Request $request, Session $session, Auth $auth, Database $db)
    {
        parent::__construct($request, $session, $auth, $db);
        $this->userService = new UserService(new User($db));
        $this->patientService = new PatientService(new Patient($db));
        $this->appointmentService = new AppointmentService(new Appointment($db));
    }

    public function dashboard(): \App\Core\Response
    {
        $stats = $this->userService->getUserStats();
        $appointmentStats = $this->appointmentService->getStats();
        
        $this->with('stats', $stats)
             ->with('appointmentStats', $appointmentStats);

        return $this->view('admin.dashboard');
    }

    public function users(): \App\Core\Response
    {
        $users = $this->userService->getAllUsers();
        $this->with('users', $users);
        return $this->view('admin.users');
    }

    public function deleteUser(int $id): \App\Core\Response
    {
        $currentUserId = $this->auth->id();
        
        if ($this->userService->deleteUser($id, $currentUserId)) {
            $this->flash('success', 'User deleted successfully.');
        } else {
            $this->flash('error', 'Failed to delete user or you cannot delete yourself.');
        }

        return $this->redirect(config('app.url') . '/admin/users');
    }

    public function patients(): \App\Core\Response
    {
        $patients = $this->patientService->getPatientsWithAppointmentCount();
        $this->with('patients', $patients);
        return $this->view('admin.patients');
    }

    public function appointments(): \App\Core\Response
    {
        $appointments = $this->appointmentService->getAllAppointments();
        $this->with('appointments', $appointments);
        return $this->view('admin.appointments');
    }
}