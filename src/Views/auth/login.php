<div class="card shadow-sm p-4" style="width: 100%; max-width: 400px; margin: 0 auto; margin-top: 10vh;">
    <h3 class="text-center mb-4 fw-bold text-primary">Clinic Portal Login</h3>

    <?php if (isset($errors['username']) || isset($errors['password'])): ?>
        <div class="alert alert-danger py-2">
            <?php foreach (['username', 'password'] as $field): ?>
                <?php if (isset($errors[$field])): ?>
                    <div><?= htmlspecialchars($errors[$field][0]) ?></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="<?= $base_url ?>/login" method="POST">
        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?? '' ?>">
        
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required placeholder="Enter username" value="<?= htmlspecialchars($old['username'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Enter password">
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Login to Portal</button>
        <div class="text-center">
            <a href="<?= $base_url ?>/register" class="text-decoration-none">Don't have an account? Sign up</a>
        </div>
    </form>
</div>