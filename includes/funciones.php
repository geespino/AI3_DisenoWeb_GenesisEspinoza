<?php
/**
 * funciones.php
 * Funciones reutilizables de seguridad y validación.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Escapa un valor antes de imprimirlo en HTML para prevenir XSS.
 */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Genera (una vez por sesión) el token CSRF del formulario.
 */
function tokenCsrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Comprueba que el token enviado coincida con el de la sesión.
 */
function csrfValido(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Limpia espacios y normaliza un texto recibido por POST.
 */
function limpiarTexto(?string $valor): string
{
    return trim(preg_replace('/\s+/u', ' ', (string) $valor));
}

/**
 * Valida los datos del formulario en el servidor.
 *
 * @param array<string, string> $datos
 * @param array<int> $idsCarreras Ids válidos de la tabla carreras.
 * @return array<string, string> Errores por campo (vacío si todo es válido).
 */
function validarEstudiante(array $datos, array $idsCarreras): array
{
    $errores = [];

    if (!preg_match('/^\d{10}$/', $datos['cedula'])) {
        $errores['cedula'] = 'La cédula debe tener exactamente 10 dígitos.';
    }

    $patronNombre = '/^[\p{L} \'-]{2,80}$/u';
    if (!preg_match($patronNombre, $datos['nombres'])) {
        $errores['nombres'] = 'Ingrese nombres válidos (solo letras, 2 a 80 caracteres).';
    }
    if (!preg_match($patronNombre, $datos['apellidos'])) {
        $errores['apellidos'] = 'Ingrese apellidos válidos (solo letras, 2 a 80 caracteres).';
    }

    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL) || mb_strlen($datos['correo']) > 120) {
        $errores['correo'] = 'Ingrese un correo electrónico válido.';
    }

    if ($datos['telefono'] !== '' && !preg_match('/^09\d{8}$/', $datos['telefono'])) {
        $errores['telefono'] = 'El celular debe tener 10 dígitos y empezar con 09.';
    }

    if (!in_array((int) $datos['carrera_id'], $idsCarreras, true)) {
        $errores['carrera_id'] = 'Seleccione una carrera de la lista.';
    }

    $semestre = filter_var($datos['semestre'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 10],
    ]);
    if ($semestre === false) {
        $errores['semestre'] = 'El semestre debe ser un número del 1 al 10.';
    }

    return $errores;
}

/**
 * Guarda un mensaje para mostrarlo una sola vez en la siguiente página.
 */
function mensajeFlash(string $tipo, string $texto): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}

/**
 * Devuelve y elimina el mensaje pendiente.
 *
 * @return array{tipo: string, texto: string}|null
 */
function obtenerFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Consulta el catálogo de carreras.
 *
 * @return array<int, array{id: int, nombre: string}>
 */
function obtenerCarreras(PDO $pdo): array
{
    return $pdo->query('SELECT id, nombre FROM carreras ORDER BY nombre')->fetchAll();
}
