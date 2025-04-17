<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/templates.php';

$vista = isset($_GET['vista']) ? $_GET['vista'] : 'inicio';

// Handle actions (e.g., login, logout, add data, etc.)
handleActions();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos Orgánicos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <!-- Barra de navegación -->
    <?php renderNavBar(); ?>

    <!-- Contenido -->
    <div class="container">
        <?php
        if ($vista === 'inicio') {
            include 'templates/home.php';
        } elseif ($vista === 'buscador') {
            include 'templates/search.php';
        } elseif ($vista === 'login') {
            include 'templates/login.php';
        } elseif ($vista === 'add_data' && isset($_SESSION['user'])) {
            include 'templates/add_data.php';
        } elseif ($vista === 'manage_accounts' && isset($_SESSION['user']) && $_SESSION['role'] === "Administrador") {
            include 'templates/manage_accounts.php';
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/scripts.js"></script>
</body>
</html>