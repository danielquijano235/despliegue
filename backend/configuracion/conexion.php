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

<<<<<<< HEAD
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
$servidor = 'sql304.infinityfree.com';
$usuario = 'if0_41316814';
$contrasena = 'camilo281';
$base_datos = 'if0_41316814_bookit';

// Origen del frontend para CORS
$frontend_url = 'https://despliegue-j2d2.onrender.com';
header("Access-Control-Allow-Origin: $frontend_url");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
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
