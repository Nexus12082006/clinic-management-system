<div class="mb-4">
    <h2>Issue Medical Record & Prescription</h2>
    <p class="text-muted">Create a new medical record and optionally add a prescription.</p>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="<?= $base_url ?>/doctor/records" method="POST">
            <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?? '' ?>">
            
            <div class="mb-3">
                <label class="form-label">Select Patient <span class="text-danger">*</span></label>
                <select name="patient_id" class="form-select" required>
                    <option value="">-- Choose Patient --</option>
                    <?php foreach ($patients as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <hr class="my-4">
            <h5 class="text-success mb-3">Medical Record Details</h5>

            <div class="mb-3">
                <label class="form-label">Diagnosis <span class="text-danger">*</span></label>
                <textarea name="diagnosis" class="form-control" rows="3" required placeholder="Enter primary diagnosis..."></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Treatment Notes</label>
                <textarea name="treatment_notes" class="form-control" rows="3" placeholder="Enter instructions or examination comments..."></textarea>
            </div>

            <hr class="my-4">
            <h5 class="text-success mb-3">Prescription Details (Optional)</h5>

            <div class="mb-3">
                <label class="form-label">Medication List</label>
                <input type="text" name="medication_list" class="form-control" placeholder="e.g., Paracetamol 500mg, Amoxicillin">
            </div>

            <div class="mb-3">
                <label class="form-label">Dosage & Duration</label>
                <input type="text" name="dosage_duration" class="form-control" placeholder="e.g., 1 tablet every 8 hours for 7 days">
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="<?= $base_url ?>/doctor" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success">Save Clinical Record</button>
            </div>
        </form>
    </div>
</div>