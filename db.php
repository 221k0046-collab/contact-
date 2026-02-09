<?php
// db.php - CONEXIÓN PARA LARAGON
header('Content-Type: text/html; charset=utf-8');

// CONFIGURACIÓN PARA LARAGON (por defecto)
$host = "localhost";      // o "127.0.0.1"
$usuario = "root";        // Usuario por defecto de Laragon
$password = "12345678";           // Contraseña vacía por defecto en Laragon
$base_datos = "contact_us";

// CREAR CONEXIÓN
$conexion = new mysqli($host, $usuario, $password, $base_datos);

// VERIFICAR CONEXIÓN
if ($conexion->connect_error) {
    die(json_encode([
        'success' => false,
        'error' => 'Error de conexión: ' . $conexion->connect_error
    ]));
}

// CONFIGURAR CHARSET
$conexion->set_charset("utf8mb4");

// OPCIONAL: Descomentar para ver mensajes de depuración
// echo "✅ Conexión exitosa a la base de datos";
?>
