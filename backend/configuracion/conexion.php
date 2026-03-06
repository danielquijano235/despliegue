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

// Orígenes permitidos para CORS (desde env). Soporta `FRONTEND_URL` o `FRONTEND_URLS` (coma-separada)
$frontend_env = getenv('FRONTEND_URL') ?: '';
$frontend_env_multi = getenv('FRONTEND_URLS') ?: '';

// Construir lista de orígenes permitidos y normalizar (sin barra final)
$allowed_origins = [];
if ($frontend_env_multi) {
    foreach (explode(',', $frontend_env_multi) as $u) {
        $u = trim($u);
        if ($u !== '') $allowed_origins[] = rtrim($u, '/');
    }
}
if ($frontend_env) {
    $allowed_origins[] = rtrim($frontend_env, '/');
}
$allowed_origins[] = 'http://localhost:3000';
$allowed_origins[] = 'http://localhost';
$allowed_origins = array_values(array_unique($allowed_origins));

// Determinar el origen de la petición y responder dinámicamente si está permitido
$request_origin = isset($_SERVER['HTTP_ORIGIN']) ? rtrim($_SERVER['HTTP_ORIGIN'], '/') : '';
if ($request_origin && in_array($request_origin, $allowed_origins, true)) {
    header('Access-Control-Allow-Origin: ' . $request_origin);
} else {
    // Fallback: usar el primer origen configurado si existe, sino localhost:3000
    $fallback = !empty($allowed_origins) ? $allowed_origins[0] : 'http://localhost:3000';
    header('Access-Control-Allow-Origin: ' . $fallback);
}
header('Vary: Origin');

// Enviar headers CORS esenciales de forma temprana para que los preflight/options reciban respuesta
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept');
header('Content-Type: application/json; charset=UTF-8');  // Default JSON

// Evitar mostrar errores en HTML y capturar errores fatales al final del script
ini_set('display_errors', '0');
error_reporting(E_ALL);
register_shutdown_function(function() use ($allowed_origins) {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        if (!headers_sent()) {
            $origin = isset($_SERVER['HTTP_ORIGIN']) ? rtrim($_SERVER['HTTP_ORIGIN'], '/') : ($allowed_origins[0] ?? 'http://localhost:3000');
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Vary: Origin');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept');
            header('Content-Type: application/json; charset=UTF-8');
        }
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error', 'detail' => $err['message']]);
        exit();
    }
});

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
// Respondemos con 200 OK y enviamos los headers CORS necesarios.
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Reafirmar headers para la respuesta preflight
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? rtrim($_SERVER['HTTP_ORIGIN'], '/') : ($allowed_origins[0] ?? 'http://localhost:3000');
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept');
    http_response_code(200);
    echo json_encode(['ok' => true]);
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
