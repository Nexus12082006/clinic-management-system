<div class="card shadow-sm p-4" style="width: 100%; max-width: 400px; margin: 0 auto; margin-top: 10vh;">
    <h3 class="text-center mb-4 fw-bold text-success">Create Account</h3>

    <?php if (isset($errors)): ?>
        <?php foreach ($errors as $field => $messages): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($msg) ?></div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <form action="<?= $base_url ?>/register" method="POST">
        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?? '' ?>">
        
        <div class="mb-3">
            <label class="form-label">Select Role</label>
            <select name="role" class="form-select" required>
                <option value="">-- Choose Role --</option>
                <option value="Administrator" <?= ($old['role'] ?? '') === 'Administrator' ? 'selected' : '' ?>>Administrator</option>
                <option value="Doctor" <?= ($old['role'] ?? '') === 'Doctor' ? 'selected' : '' ?>>Doctor</option>
                <option value="Receptionist" <?= ($old['role'] ?? '') === 'Receptionist' ? 'selected' : '' ?>>Receptionist</option>
                <option value="Patient" <?= ($old['role'] ?? '') === 'Patient' ? 'selected' : '' ?>>Patient</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required placeholder="Choose a username" value="<?= htmlspecialchars($old['username'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Choose a password">
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required placeholder="Confirm password">
        </div>
        <button type="submit" class="btn btn-success w-100 py-2 mb-3">Register Account</button>
        <div class="text-center">
            <a href="<?= $base_url ?>/login" class="text-decoration-none">Already have an account? Login</a>
        </div>
    </form>
</div>