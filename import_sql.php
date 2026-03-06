<?php
/**
 * import_sql.php
 * Uso: exportar/definir las env vars DB_HOST, DB_PORT, DB_USER, DB_PASS, DB_NAME
 * y ejecutar: php import_sql.php
 */

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = intval(getenv('DB_PORT') ?: 3306);
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'bookit';

$sqlFile = __DIR__ . '/base-datos/bookit.sql';

if (!file_exists($sqlFile)) {
    fwrite(STDERR, "ERROR: SQL file not found: $sqlFile\n");
    exit(1);
}

$sql = file_get_contents($sqlFile);
if ($sql === false) {
    fwrite(STDERR, "ERROR: Could not read SQL file.\n");
    exit(1);
}

$mysqli = mysqli_init();
if (!$mysqli) {
    fwrite(STDERR, "ERROR: mysqli_init failed\n");
    exit(1);
}

// conectar con puerto
if (!@$mysqli->real_connect($host, $user, $pass, $db, $port)) {
    fwrite(STDERR, "ERROR: Connect failed: ({$mysqli->connect_errno}) {$mysqli->connect_error}\n");
    exit(1);
}

// Ejecutar múltiples queries
if (!$mysqli->multi_query($sql)) {
    fwrite(STDERR, "ERROR: Multi query failed: ({$mysqli->errno}) {$mysqli->error}\n");
    $mysqli->close();
    exit(1);
}

// Consumir todos los resultados
do {
    if ($res = $mysqli->store_result()) {
        $res->free();
    }
} while ($mysqli->more_results() && $mysqli->next_result());

if ($mysqli->errno) {
    fwrite(STDERR, "WARNING: Finished with errors: ({$mysqli->errno}) {$mysqli->error}\n");
} else {
    fwrite(STDOUT, "Import completed successfully.\n");
}

$mysqli->close();

return 0;
