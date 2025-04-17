<?php
// Database connection settings
$host = 'localhost';
$dbname = 'organic_products';
$username = 'root'; // Replace with your MySQL username
$password = '';     // Replace with your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Fetch initial data from the database
function getDepartments() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM departments");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMunicipalities($department_id = null) {
    global $pdo;
    if ($department_id) {
        $stmt = $pdo->prepare("SELECT * FROM municipalities WHERE department_id = ?");
        $stmt->execute([$department_id]);
    } else {
        $stmt = $pdo->query("SELECT * FROM municipalities");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM products");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$departments = getDepartments();
$products = getProducts();
?>