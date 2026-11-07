<?php
/**
 * index.php
 * Vista del formulario de registro de estudiantes (Create).
 */

declare(strict_types=1);

require __DIR__ . '/config/conexion.php';
require __DIR__ . '/includes/funciones.php';

$pdo      = obtenerConexion();
$carreras = obtenerCarreras($pdo);

// Datos y errores devueltos por guardar.php cuando la validación falla.
$viejo   = $_SESSION['viejo'] ?? [];
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['viejo'], $_SESSION['errores']);

$valor = fn (string $campo): string => e($viejo[$campo] ?? '');
$error = fn (string $campo): string => isset($errores[$campo])
    ? '<p class="campo__error">' . e($errores[$campo]) . '</p>'
    : '';
$claseError = fn (string $campo): string => isset($errores[$campo]) ? ' campo--invalido' : '';

$tituloPagina = 'Registrar estudiante';
$paginaActiva = 'registro';
require __DIR__ . '/includes/header.php';
?>
        <section class="panel">
            <h2 class="panel__titulo">Nuevo estudiante</h2>
            <p class="panel__texto">Los campos marcados con <span class="obligatorio">*</span> son obligatorios.</p>

            <form class="formulario" action="guardar.php" method="post" novalidate data-validar>
                <input type="hidden" name="csrf_token" value="<?= e(tokenCsrf()) ?>">

                <div class="campo">
                    <label class="campo__etiqueta" for="cedula">Cédula <span class="obligatorio">*</span></label>
                    <input class="campo__control<?= $claseError('cedula') ?>" type="text" id="cedula" name="cedula"
                           value="<?= $valor('cedula') ?>" required inputmode="numeric" pattern="\d{10}" maxlength="10"
                           placeholder="0912345678" title="10 dígitos numéricos">
                    <?= $error('cedula') ?>
                </div>

                <div class="campo">
                    <label class="campo__etiqueta" for="correo">Correo electrónico <span class="obligatorio">*</span></label>
                    <input class="campo__control<?= $claseError('correo') ?>" type="email" id="correo" name="correo"
                           value="<?= $valor('correo') ?>" required maxlength="120" placeholder="nombre@ejemplo.com">
                    <?= $error('correo') ?>
                </div>

                <div class="campo">
                    <label class="campo__etiqueta" for="nombres">Nombres <span class="obligatorio">*</span></label>
                    <input class="campo__control<?= $claseError('nombres') ?>" type="text" id="nombres" name="nombres"
                           value="<?= $valor('nombres') ?>" required minlength="2" maxlength="80"
                           pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ' \-]{2,80}" title="Solo letras">
                    <?= $error('nombres') ?>
                </div>

                <div class="campo">
                    <label class="campo__etiqueta" for="apellidos">Apellidos <span class="obligatorio">*</span></label>
                    <input class="campo__control<?= $claseError('apellidos') ?>" type="text" id="apellidos" name="apellidos"
                           value="<?= $valor('apellidos') ?>" required minlength="2" maxlength="80"
                           pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ' \-]{2,80}" title="Solo letras">
                    <?= $error('apellidos') ?>
                </div>

                <div class="campo">
                    <label class="campo__etiqueta" for="carrera_id">Carrera <span class="obligatorio">*</span></label>
                    <select class="campo__control<?= $claseError('carrera_id') ?>" id="carrera_id" name="carrera_id" required>
                        <option value="">Seleccione una carrera…</option>
<?php foreach ($carreras as $carrera): ?>
                        <option value="<?= e($carrera['id']) ?>" <?= (string) ($viejo['carrera_id'] ?? '') === (string) $carrera['id'] ? 'selected' : '' ?>><?= e($carrera['nombre']) ?></option>
<?php endforeach; ?>
                    </select>
                    <?= $error('carrera_id') ?>
                </div>

                <div class="campo campo--corto">
                    <label class="campo__etiqueta" for="semestre">Semestre <span class="obligatorio">*</span></label>
                    <input class="campo__control<?= $claseError('semestre') ?>" type="number" id="semestre" name="semestre"
                           value="<?= $valor('semestre') ?>" required min="1" max="10" step="1" placeholder="1 - 10">
                    <?= $error('semestre') ?>
                </div>

                <div class="campo campo--corto">
                    <label class="campo__etiqueta" for="telefono">Celular</label>
                    <input class="campo__control<?= $claseError('telefono') ?>" type="tel" id="telefono" name="telefono"
                           value="<?= $valor('telefono') ?>" pattern="09\d{8}" maxlength="10" placeholder="0991234567"
                           title="10 dígitos, empieza con 09">
                    <?= $error('telefono') ?>
                </div>

                <div class="formulario__acciones">
                    <button class="boton" type="submit">Registrar estudiante</button>
                    <button class="boton boton--secundario" type="reset">Limpiar</button>
                </div>
            </form>
        </section>

        <script>
            // Validación en el cliente: muestra los mensajes del navegador antes de enviar.
            document.querySelector('[data-validar]').addEventListener('submit', (evento) => {
                const formulario = evento.target;
                if (!formulario.checkValidity()) {
                    evento.preventDefault();
                    formulario.reportValidity();
                }
            });
        </script>
<?php require __DIR__ . '/includes/footer.php'; ?>
