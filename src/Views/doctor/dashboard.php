<div class="mb-4">
    <h2>Doctor Workspace</h2>
    <p class="text-muted">Manage patient diagnoses, view clinical schedules, and file medical notes.</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-start border-primary border-4">
            <div class="card-body">
                <h6 class="text-muted">Total Registered Patients</h6>
                <h3 class="fw-bold"><?= $patientCount ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-start border-warning border-4">
            <div class="card-body">
                <h6 class="text-muted">Total Appointments</h6>
                <h3 class="fw-bold"><?= $appointmentCount ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-start border-success border-4">
            <div class="card-body">
                <h6 class="text-muted">Medical Records Filed</h6>
                <h3 class="fw-bold"><?= $recordCount ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body bg-white rounded-3 p-4">
        <h5 class="card-title fw-bold text-dark mb-3">Clinical Quick Actions</h5>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= $base_url ?>/doctor/prescriptions/create" class="btn btn-primary px-4 py-2 shadow-sm">+ Prescribe Medicines</a>
            <a href="<?= $base_url ?>/doctor/records/create" class="btn btn-outline-primary px-4 py-2">+ New Medical Record</a>
            <a href="<?= $base_url ?>/admin/patients" class="btn btn-outline-secondary px-4 py-2">View Patient Directory</a>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Patient Check-up Queue</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Patient Name</th>
                        <th>Date / Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($appointments)): ?>
                        <?php foreach ($appointments as $row): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($row['patient_name']) ?></td>
                                <td><?= htmlspecialchars($row['appointment_date'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if (($row['status'] ?? 'Scheduled') === 'Completed'): ?>
                                        <span class="badge bg-success">Completed</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Scheduled / In Progress</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (($row['status'] ?? 'Scheduled') !== 'Completed'): ?>
                                        <a href="<?= $base_url ?>/doctor/appointments/<?= $row['id'] ?>/complete" class="btn btn-success btn-sm">Mark Done</a>
                                    <?php else: ?>
                                        <span class="text-muted small">Finished</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No appointments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>