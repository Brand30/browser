<?php
function handleActions() {
    global $pdo;

    // Manejo de login
    if (isset($_POST['login'])) {
        $correo = $_POST['correo'];
        $password = $_POST['password'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE correo = ?");
        $stmt->execute([$correo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && $user['password'] === $password) { // In production, use password_hash and password_verify
            $_SESSION['user'] = $user['correo'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
        } else {
            global $login_error;
            $login_error = "Credenciales incorrectas.";
        }
    }

    // Manejo de logout
    if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: index.php");
    }

    // Agregar datos (múltiples productos)
    if (isset($_POST['add_data'])) {
        $department_id = $_POST['department'];
        $municipality_id = $_POST['municipality'];
        $proveedor = $_POST['proveedor'];
        $numero = $_POST['numero'];
        $ubicacion = $_POST['ubicacion'];
        $added_by = $_SESSION['user_id'];
        $products = $_POST['products'] ?? [];
        $quantities = $_POST['quantities'] ?? [];
        $units = $_POST['units'] ?? [];

        foreach ($products as $index => $product_id) {
            if (!empty($product_id)) { // Solo procesar si se seleccionó un producto
                $cantidad = isset($quantities[$index]) && $quantities[$index] !== '' ? floatval($quantities[$index]) : 0;
                $unidad = isset($units[$index]) && $units[$index] !== '' ? $units[$index] : 'kg'; // Default to 'kg' if not specified
                $stmt = $pdo->prepare("INSERT INTO user_data (department_id, municipality_id, product_id, added_by, proveedor, numero, ubicacion, cantidad, unidad) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$department_id, $municipality_id, $product_id, $added_by, $proveedor, $numero, $ubicacion, $cantidad, $unidad]);
            }
        }
        header("Location: index.php?vista=add_data");
    }

    // Eliminar datos (individual)
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $stmt = $pdo->prepare("SELECT * FROM user_data WHERE id = ?");
        $stmt->execute([$id]);
        $data_entry = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data_entry && ($_SESSION['role'] === "Administrador" || ($_SESSION['role'] === "Técnico" && $data_entry['added_by'] == $_SESSION['user_id']))) {
            $stmt = $pdo->prepare("DELETE FROM user_data WHERE id = ?");
            $stmt->execute([$id]);
        }
        header("Location: index.php?vista=add_data");
    }

    // Eliminar todos los datos
    if (isset($_GET['delete_all'])) {
        if ($_SESSION['role'] === "Administrador") {
            $stmt = $pdo->prepare("DELETE FROM user_data");
            $stmt->execute();
        } elseif ($_SESSION['role'] === "Técnico") {
            $stmt = $pdo->prepare("DELETE FROM user_data WHERE added_by = ?");
            $stmt->execute([$_SESSION['user_id']]);
        }
        header("Location: index.php?vista=add_data");
    }

    // Editar datos (individual o todos)
    if (isset($_POST['edit_data'])) {
        $ids = $_POST['ids'] ?? [];
        $department_ids = $_POST['department'] ?? [];
        $municipality_ids = $_POST['municipality'] ?? [];
        $product_ids = $_POST['product'] ?? [];
        $proveedors = $_POST['proveedor'] ?? [];
        $numeros = $_POST['numero'] ?? [];
        $ubicacions = $_POST['ubicacion'] ?? [];
        $cantidades = $_POST['cantidad'] ?? [];
        $unidades = $_POST['unidad'] ?? [];

        foreach ($ids as $index => $id) {
            // Verificar permisos para cada entrada
            $stmt = $pdo->prepare("SELECT * FROM user_data WHERE id = ?");
            $stmt->execute([$id]);
            $data_entry = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data_entry && ($_SESSION['role'] === "Administrador" || ($_SESSION['role'] === "Técnico" && $data_entry['added_by'] == $_SESSION['user_id']))) {
                $stmt = $pdo->prepare("UPDATE user_data SET department_id = ?, municipality_id = ?, product_id = ?, proveedor = ?, numero = ?, ubicacion = ?, cantidad = ?, unidad = ? WHERE id = ?");
                $stmt->execute([
                    $department_ids[$index],
                    $municipality_ids[$index],
                    $product_ids[$index],
                    $proveedors[$index],
                    $numeros[$index],
                    $ubicacions[$index],
                    $cantidades[$index] !== '' ? floatval($cantidades[$index]) : 0,
                    $unidades[$index],
                    $id
                ]);
            }
        }
        header("Location: index.php?vista=add_data");
    }

    // Gestionar cuentas (solo para administrador)
    if (isset($_POST['manage_account']) && $_SESSION['role'] === "Administrador") {
        $correo = $_POST['correo'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $numero = $_POST['numero'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        $stmt = $pdo->prepare("INSERT INTO users (correo, nombre, apellido, numero, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$correo, $nombre, $apellido, $numero, $password, $role]);
        header("Location: index.php?vista=manage_accounts");
    }

    // Eliminar cuenta (solo para administrador)
    if (isset($_GET['delete_user']) && $_SESSION['role'] === "Administrador") {
        $user_to_delete = $_GET['delete_user'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE correo = ?");
        $stmt->execute([$user_to_delete]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && $user['correo'] !== $_SESSION['user']) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE correo = ?");
            $stmt->execute([$user_to_delete]);
        }
        header("Location: index.php?vista=manage_accounts");
    }
}

function renderNavBar() {
    ?>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="?vista=inicio">Productos Orgánicos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="?vista=inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?vista=buscador">Buscador</a>
                    </li>
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="?vista=add_data">Agregar Datos</a>
                        </li>
                        <?php if ($_SESSION['role'] === "Administrador"): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="?vista=manage_accounts">Gestionar Cuentas</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="?logout=true">Cerrar Sesión (<?php echo $_SESSION['user']; ?>)</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="?vista=login">Iniciar Sesión</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <?php
}
?>