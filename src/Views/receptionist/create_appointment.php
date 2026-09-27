<div class="mb-4">
    <h2>Schedule New Appointment</h2>
    <p class="text-muted">Book an appointment for a patient.</p>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?= $base_url ?>/receptionist/appointments" method="POST">
            <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?? '' ?>">
            
            <div class="mb-3">
                <label class="form-label">Select Patient</label>
                <select name="patient_id" class="form-select" required>
                    <option value="">-- Choose Patient --</option>
                    <?php foreach ($patients as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['phone']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Appointment Date & Time</label>
                <input type="datetime-local" name="appointment_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Notes / Symptoms</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Enter reason for visit..."></textarea>
            </div>
            <div class="d-flex justify-content-between">
                <a href="<?= $base_url ?>/receptionist" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Appointment</button>
            </div>
        </form>
    </div>
</div>