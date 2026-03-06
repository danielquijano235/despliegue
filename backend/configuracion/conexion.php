<?php
/**
 * ============================================
 * BOOKIT - Configuración de Conexión a Base de Datos
 * Archivo: configuracion/conexion.php
 * ============================================
 * 
 * Este archivo establece la conexión con la base de datos MySQL
 * y configura los headers necesarios para que React pueda
 * comunicarse con el backend (CORS).
 * 
 * Se incluye al inicio de todos los demás archivos PHP.
 */

// ============================================
// DATOS DE CONEXIÓN A LA BASE DE DATOS (desde ENV)
// En producción se recomienda configurar las variables de entorno
// DB_HOST, DB_PORT, DB_USER, DB_PASS, DB_NAME y FRONTEND_URL
// ============================================
$servidor = getenv('DB_HOST') ?: 'localhost';
$puerto = intval(getenv('DB_PORT') ?: 3306);
$usuario = getenv('DB_USER') ?: 'root';
$contrasena = getenv('DB_PASS') ?: '';
$base_datos = getenv('DB_NAME') ?: 'bookit';

// Origen del frontend para CORS (ej. https://mi-frontend.onrender.com)
// Valor por defecto para desarrollo
$frontend_url = getenv('FRONTEND_URL') ?: 'http://localhost:3000';

// Construir lista de orígenes permitidos (allowlist)
$allowed_origins = array_filter([
    $frontend_url,
    'http://localhost:3000',
    'http://localhost'
]);

// Determinar el origen de la petición y responder dinámicamente si está permitido
$request_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if ($request_origin && in_array($request_origin, $allowed_origins, true)) {
    header('Access-Control-Allow-Origin: ' . $request_origin);
} else {
    // Fallback a la URL configurada (útil para herramientas que no envían Origin)
    header('Access-Control-Allow-Origin: ' . $frontend_url);
}
header('Vary: Origin');

// ============================================
// CREAR CONEXIÓN CON LA BASE DE DATOS
// Usamos mysqli_connect para conectar PHP con MySQL
// ============================================
$conexion = mysqli_connect($servidor, $usuario, $contrasena, $base_datos, $puerto);

// Verificar si la conexión fue exitosa
if (!$conexion) {
    // Si falla, mostrar error y detener el script
    die("Error de conexión: " . mysqli_connect_error());
}

// Configurar la codificación a UTF-8 (utf8mb4) para soportar todos los caracteres
mysqli_set_charset($conexion, "utf8mb4");
// Asegurar también la conexión con SET NAMES en caso de capas intermedias
mysqli_query($conexion, "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");

// ============================================
// CONFIGURACIÓN DE CORS (Cross-Origin Resource Sharing)
// Leemos el origen permitido desde la variable FRONTEND_URL
// ============================================
// Métodos, headers y credenciales permitidas
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");  // Todas las respuestas serán JSON

// ============================================
// MANEJAR PETICIONES PREFLIGHT (OPTIONS)
// El navegador envía una petición OPTIONS antes de
// la petición real para verificar si tiene permisos.
// Respondemos con 200 OK para que continúe.
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ============================================
// INICIAR SESIÓN PHP
// Configuramos parámetros de la cookie de sesión para permitir
// su uso en cross-site cuando el backend está detrás de un proxy HTTPS
// (localtunnel, ngrok, Render, etc.). Se detecta HTTPS mediante
// variables como HTTPS o X-Forwarded-Proto.
// ============================================
// Detectar si la petición original fue sobre HTTPS (proxy incluido)
$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on');

// Ajustar cookie params: SameSite=None y secure cuando sea HTTPS
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $is_https,
    'httponly' => true,
    'samesite' => 'None'
]);

session_start();
?>
