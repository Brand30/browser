<?php
header('Content-Type: application/json');
require_once 'config.php';

$department_id = isset($_GET['department_id']) ? (int)$_GET['department_id'] : 0;
$municipalities = getMunicipalities($department_id);
echo json_encode($municipalities);
?>