<?php
$users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card p-4">
    <h2 class="mb-4">Gestionar Cuentas</h2>
    <form method="POST">
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Correo</label>
                <input type="email" name="correo" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Apellido</label>
                <input type="text" name="apellido" class="form-control" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Número</label>
                <input type="text" name="numero" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Rol</label>
                <select name="role" class="form-select" required>
                    <option value="Administrador">Administrador</option>
                    <option value="Técnico">Técnico</option>
                </select>
            </div>
        </div>
        <button type="submit" name="manage_account" class="btn btn-primary">Crear Cuenta</button>
    </form>
    <hr>
    <h3>Cuentas Existentes</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Correo</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Número</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['correo']; ?></td>
                        <td><?php echo $user['nombre']; ?></td>
                        <td><?php echo $user['apellido']; ?></td>
                        <td><?php echo $user['numero']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td>
                            <a href="?vista=manage_accounts&delete_user=<?php echo $user['correo']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No hay cuentas disponibles.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>