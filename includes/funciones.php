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
