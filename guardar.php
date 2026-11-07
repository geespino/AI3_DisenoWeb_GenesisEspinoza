<?php
/**
 * guardar.php
 * Procesa el formulario: valida en el servidor e inserta con sentencias preparadas (PDO).
 */

declare(strict_types=1);

require __DIR__ . '/config/conexion.php';
require __DIR__ . '/includes/funciones.php';

// Solo se aceptan envíos por POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Protección CSRF: el formulario debe venir de nuestra propia página.
if (!csrfValido($_POST['csrf_token'] ?? null)) {
    mensajeFlash('error', 'La sesión expiró o el formulario no es válido. Inténtelo de nuevo.');
    header('Location: index.php');
    exit;
}

$datos = [
    'cedula'     => limpiarTexto($_POST['cedula'] ?? ''),
    'nombres'    => limpiarTexto($_POST['nombres'] ?? ''),
    'apellidos'  => limpiarTexto($_POST['apellidos'] ?? ''),
    'correo'     => mb_strtolower(limpiarTexto($_POST['correo'] ?? '')),
    'telefono'   => limpiarTexto($_POST['telefono'] ?? ''),
    'carrera_id' => limpiarTexto($_POST['carrera_id'] ?? ''),
    'semestre'   => limpiarTexto($_POST['semestre'] ?? ''),
];

$pdo = obtenerConexion();
$idsCarreras = array_map('intval', array_column(obtenerCarreras($pdo), 'id'));

// Validación en el servidor (no se confía en la validación del navegador).
$errores = validarEstudiante($datos, $idsCarreras);
if ($errores !== []) {
    $_SESSION['viejo']   = $datos;
    $_SESSION['errores'] = $errores;
    mensajeFlash('error', 'Revise los campos marcados.');
    header('Location: index.php');
    exit;
}

// Sentencia preparada: los datos viajan separados del SQL, por eso no pueden inyectar código.
$sql = 'INSERT INTO estudiantes (cedula, nombres, apellidos, correo, telefono, carrera_id, semestre)
        VALUES (:cedula, :nombres, :apellidos, :correo, :telefono, :carrera_id, :semestre)';

try {
    $sentencia = $pdo->prepare($sql);
    $sentencia->bindValue(':cedula', $datos['cedula'], PDO::PARAM_STR);
    $sentencia->bindValue(':nombres', $datos['nombres'], PDO::PARAM_STR);
    $sentencia->bindValue(':apellidos', $datos['apellidos'], PDO::PARAM_STR);
    $sentencia->bindValue(':correo', $datos['correo'], PDO::PARAM_STR);
    $sentencia->bindValue(':telefono', $datos['telefono'] === '' ? null : $datos['telefono'],
        $datos['telefono'] === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $sentencia->bindValue(':carrera_id', (int) $datos['carrera_id'], PDO::PARAM_INT);
    $sentencia->bindValue(':semestre', (int) $datos['semestre'], PDO::PARAM_INT);
    $sentencia->execute();

    unset($_SESSION['csrf_token']); // Se renueva el token tras un registro exitoso.
    mensajeFlash('exito', 'Estudiante registrado correctamente.');
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    // 23000 = violación de restricción (cédula o correo repetidos).
    if ($e->getCode() === '23000') {
        $_SESSION['viejo'] = $datos;
        mensajeFlash('error', 'Ya existe un estudiante registrado con esa cédula o ese correo.');
    } else {
        registrarError('Error al insertar estudiante: ' . $e->getMessage());
        mensajeFlash('error', 'No se pudo guardar el registro. Inténtelo más tarde.');
    }
    header('Location: index.php');
    exit;
}
