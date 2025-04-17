<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4">
            <h2 class="mb-4">Iniciar Sesión</h2>
            <?php if (isset($login_error)): ?>
                <div class="alert alert-danger"><?php echo $login_error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary">Iniciar Sesión</button>
            </form>
        </div>
    </div>
</div>