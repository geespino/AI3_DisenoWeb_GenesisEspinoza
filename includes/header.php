<?php
/** @var string $tituloPagina */
/** @var string $paginaActiva */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($tituloPagina) ?> | Sistema de Registro de Estudiantes</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header class="encabezado">
        <div class="contenedor encabezado__contenido">
            <div>
                <p class="encabezado__marca">Sistema Web · PHP + MariaDB</p>
                <h1 class="encabezado__titulo">Registro de Estudiantes</h1>
            </div>
            <nav class="menu" aria-label="Menú principal">
                <a class="menu__enlace <?= $paginaActiva === 'registro' ? 'menu__enlace--activo' : '' ?>" href="index.php">Registrar</a>
            </nav>
        </div>
    </header>
    <main class="contenedor principal">
<?php $flash = obtenerFlash(); ?>
<?php if ($flash !== null): ?>
        <div class="alerta alerta--<?= e($flash['tipo']) ?>" role="alert"><?= e($flash['texto']) ?></div>
<?php endif; ?>
