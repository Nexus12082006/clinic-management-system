<div class="mb-4">
    <h2>Add New Patient</h2>
    <p class="text-muted">Register a new patient in the system.</p>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?= $base_url ?>/receptionist/patients" method="POST">
            <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?? '' ?>">
            
            <div class="mb-3">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($old['name'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address <span class="text-muted">(Optional)</span></label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Address <span class="text-muted">(Optional)</span></label>
                <input type="text" name="address" class="form-control" placeholder="Enter residential address" value="<?= htmlspecialchars($old['address'] ?? '') ?>">
            </div>
            <div class="d-flex justify-content-between">
                <a href="<?= $base_url ?>/receptionist" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Patient</button>
            </div>
        </form>
    </div>
</div>