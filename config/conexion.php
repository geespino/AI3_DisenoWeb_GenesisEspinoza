<?php
/**
 * conexion.php
 * Módulo de conexión a la base de datos con PDO (PHP Data Objects).
 *
 * - Las credenciales viven solo en este archivo (o en config.local.php, que no se sube a GitHub).
 * - Si la conexión falla, el detalle técnico se guarda en logs/app.log y al usuario
 *   se le muestra un mensaje genérico: nunca se exponen credenciales ni mensajes del sistema.
 */

declare(strict_types=1);

date_default_timezone_set('America/Guayaquil');

// Valores por defecto de XAMPP (usuario root sin contraseña).
$config = [
    'host'     => 'localhost',
    'puerto'   => 3306,
    'base'     => 'db_sistema_web',
    'usuario'  => 'root',
    'clave'    => '',
    'charset'  => 'utf8mb4',
];

// Si existe un archivo local con otras credenciales, se usan esas (está en .gitignore).
$configLocal = __DIR__ . '/config.local.php';
if (is_file($configLocal)) {
    $config = array_merge($config, require $configLocal);
}

/**
 * Devuelve una única conexión PDO para toda la petición (patrón Singleton sencillo).
 */
function obtenerConexion(): PDO
{
    static $pdo = null;
    global $config;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $config['host'],
        $config['puerto'],
        $config['base'],
        $config['charset']
    );

    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Errores como excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Resultados como arreglos asociativos
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Sentencias preparadas reales del servidor
    ];

    try {
        $pdo = new PDO($dsn, $config['usuario'], $config['clave'], $opciones);
        return $pdo;
    } catch (PDOException $e) {
        registrarError('Fallo de conexión a la base de datos: ' . $e->getMessage());
        mostrarErrorGenerico();
    }
}

/**
 * Guarda el detalle técnico del error en logs/app.log (carpeta ignorada por Git).
 */
function registrarError(string $mensaje): void
{
    $archivo = dirname(__DIR__) . '/logs/app.log';
    $linea = sprintf("[%s] %s%s", date('Y-m-d H:i:s'), $mensaje, PHP_EOL);
    error_log($linea, 3, $archivo);
}

/**
 * Muestra una página de error amigable y detiene la ejecución.
 */
function mostrarErrorGenerico(): never
{
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">'
        . '<title>Servicio no disponible</title></head><body style="font-family:sans-serif;padding:2rem">'
        . '<h1>Servicio no disponible</h1>'
        . '<p>No fue posible conectar con la base de datos. Intente nuevamente más tarde.</p>'
        . '</body></html>';
    exit;
}
