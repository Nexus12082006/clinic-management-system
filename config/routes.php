<?php

return function (\App\Core\Router $router) {
    $router->get('/', 'AuthController@login');
    $router->get('/login', 'AuthController@login');
    $router->post('/login', 'AuthController@login');
    $router->get('/register', 'AuthController@register');
    $router->post('/register', 'AuthController@register');
    $router->post('/logout', 'AuthController@logout');

    $router->group(['middleware' => ['auth', 'role:Administrator']], function ($router) {
        $router->get('/admin', 'AdminController@dashboard');
        $router->get('/admin/users', 'AdminController@users');
        $router->delete('/admin/users/{id}', 'AdminController@deleteUser');
        $router->get('/admin/patients', 'AdminController@patients');
        $router->get('/admin/appointments', 'AdminController@appointments');
    });

    $router->group(['middleware' => ['auth', 'role:Doctor']], function ($router) {
        $router->get('/doctor', 'DoctorController@dashboard');
        $router->get('/doctor/appointments', 'DoctorController@appointments');
        $router->post('/doctor/appointments/{id}/complete', 'DoctorController@completeAppointment');
        $router->get('/doctor/prescriptions/create', 'DoctorController@createPrescription');
        $router->post('/doctor/prescriptions', 'DoctorController@storePrescription');
        $router->get('/doctor/records/create', 'DoctorController@createRecord');
        $router->post('/doctor/records', 'DoctorController@storeRecord');
    });

    $router->group(['middleware' => ['auth', 'role:Receptionist']], function ($router) {
        $router->get('/receptionist', 'ReceptionistController@dashboard');
        $router->get('/receptionist/patients/create', 'ReceptionistController@createPatient');
        $router->post('/receptionist/patients', 'ReceptionistController@storePatient');
        $router->get('/receptionist/appointments/create', 'ReceptionistController@createAppointment');
        $router->post('/receptionist/appointments', 'ReceptionistController@storeAppointment');
        $router->post('/receptionist/appointments/{id}/complete', 'ReceptionistController@completeAppointment');
        $router->delete('/receptionist/patients/{id}', 'ReceptionistController@deletePatient');
    });

    $router->group(['middleware' => ['auth', 'role:Patient']], function ($router) {
        $router->get('/patient', 'PatientController@dashboard');
        $router->get('/patient/appointments', 'PatientController@appointments');
        $router->get('/patient/records', 'PatientController@records');
        $router->get('/patient/prescriptions', 'PatientController@prescriptions');
    });
};