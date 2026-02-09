<?php
// procesar_formulario.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Incluir la conexión
require_once 'db.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Obtener datos del formulario
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email_address = isset($_POST['email']) ? trim($_POST['email']) : '';
$query_type = isset($_POST['query_type']) ? $_POST['query_type'] : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$consent = isset($_POST['consent']) ? 1 : 0;

// VALIDACIONES EN EL SERVIDOR
$errores = [];

if (empty($first_name)) {
    $errores[] = "El nombre es requerido";
}

if (empty($last_name)) {
    $errores[] = "El apellido es requerido";
}

if (empty($email_address) || !filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "Email inválido";
}

if (empty($query_type) || !in_array($query_type, ['general', 'support'])) {
    $errores[] = "Tipo de consulta inválido";
}

if (empty($message)) {
    $errores[] = "El mensaje es requerido";
}

if ($consent != 1) {
    $errores[] = "Debes aceptar el consentimiento";
}

// Si hay errores, devolver
if (!empty($errores)) {
    echo json_encode([
        'success' => false,
        'error' => 'Errores de validación',
        'errores' => $errores
    ]);
    exit;
}

// PREPARAR LA CONSULTA SQL - INCLUYENDO created_at
$sql = "INSERT INTO contacto (first_name, last_name, email_address, query_type, message, consent, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'error' => 'Error al preparar la consulta: ' . $conexion->error
    ]);
    exit;
}

// Vincular parámetros (6 valores: 5 strings y 1 integer)
$stmt->bind_param("sssssi", $first_name, $last_name, $email_address, $query_type, $message, $consent);

// EJECUTAR
if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Formulario enviado correctamente',
        'id' => $stmt->insert_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Error al guardar: ' . $stmt->error
    ]);
}

// Cerrar
$stmt->close();
$conexion->close();
?>