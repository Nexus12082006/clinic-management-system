<div class="mb-4">
    <h2>Prescribe Medicine List</h2>
    <p class="text-muted">Specify the medicines and instructions for the patient to take.</p>
</div>

<div class="card shadow-sm p-4">
    <form action="<?= $base_url ?>/doctor/prescriptions" method="POST">
        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?? '' ?>">
        
        <div class="mb-3">
            <label class="form-label fw-bold">Select Patient</label>
            <select name="patient_id" class="form-select" required>
                <option value="">-- Choose Patient --</option>
                <?php foreach ($patients as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">List of Medicines to Take</label>
            <textarea name="medicines" class="form-control" rows="4" required placeholder="e.g., 1. Paracetamol 500mg - 1 tablet every 6 hours&#10;2. Amoxicillin 250mg - 1 capsule 3x a day"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Additional Clinical Instructions / Notes</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="e.g., Drink plenty of water, rest for 3 days."></textarea>
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?= $base_url ?>/doctor" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary w-100 py-2">Save & Issue Prescription</button>
        </div>
    </form>
</div>