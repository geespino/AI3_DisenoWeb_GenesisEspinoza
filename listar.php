<?php
/**
 * listar.php
 * Consulta y muestra los estudiantes registrados (Read).
 * Toda salida se escapa con htmlspecialchars() (función e()) para prevenir XSS.
 */

declare(strict_types=1);

require __DIR__ . '/config/conexion.php';
require __DIR__ . '/includes/funciones.php';

$pdo = obtenerConexion();
$busqueda = limpiarTexto($_GET['q'] ?? '');

$sql = 'SELECT e.id, e.cedula, e.nombres, e.apellidos, e.correo, e.telefono,
               c.nombre AS carrera, e.semestre, e.fecha_creacion
        FROM estudiantes AS e
        INNER JOIN carreras AS c ON c.id = e.carrera_id';

$parametros = [];
if ($busqueda !== '') {
    // La búsqueda también usa parámetros: el texto del usuario nunca se concatena al SQL.
    $sql .= ' WHERE e.apellidos LIKE :texto OR e.nombres LIKE :texto2 OR e.cedula LIKE :texto3';
    $comodin = '%' . $busqueda . '%';
    $parametros = [':texto' => $comodin, ':texto2' => $comodin, ':texto3' => $comodin];
}
$sql .= ' ORDER BY e.fecha_creacion DESC, e.id DESC';

$sentencia = $pdo->prepare($sql);
$sentencia->execute($parametros);
$estudiantes = $sentencia->fetchAll();

$tituloPagina = 'Estudiantes registrados';
$paginaActiva = 'listado';
require __DIR__ . '/includes/header.php';
?>
        <section class="panel">
            <div class="panel__cabecera">
                <h2 class="panel__titulo">Estudiantes registrados</h2>
                <span class="contador"><?= e(count($estudiantes)) ?> registro(s)</span>
            </div>

            <form class="busqueda" method="get" action="listar.php" role="search">
                <input class="campo__control" type="search" name="q" value="<?= e($busqueda) ?>"
                       placeholder="Buscar por nombres, apellidos o cédula" aria-label="Buscar estudiantes">
                <button class="boton" type="submit">Buscar</button>
<?php if ($busqueda !== ''): ?>
                <a class="boton boton--secundario" href="listar.php">Ver todos</a>
<?php endif; ?>
            </form>

<?php if ($estudiantes === []): ?>
            <p class="vacio">
                <?= $busqueda !== '' ? 'No se encontraron estudiantes para "' . e($busqueda) . '".' : 'Aún no hay estudiantes registrados.' ?>
                <a href="index.php">Registrar el primero</a>
            </p>
<?php else: ?>
            <div class="tabla-responsiva">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Cédula</th>
                            <th scope="col">Estudiante</th>
                            <th scope="col">Correo</th>
                            <th scope="col">Celular</th>
                            <th scope="col">Carrera</th>
                            <th scope="col">Sem.</th>
                            <th scope="col">Registrado</th>
                        </tr>
                    </thead>
                    <tbody>
<?php foreach ($estudiantes as $estudiante): ?>
                        <tr>
                            <td data-titulo="#"><?= e($estudiante['id']) ?></td>
                            <td data-titulo="Cédula"><?= e($estudiante['cedula']) ?></td>
                            <td data-titulo="Estudiante"><?= e($estudiante['apellidos'] . ', ' . $estudiante['nombres']) ?></td>
                            <td data-titulo="Correo"><?= e($estudiante['correo']) ?></td>
                            <td data-titulo="Celular"><?= e($estudiante['telefono'] ?? '—') ?></td>
                            <td data-titulo="Carrera"><?= e($estudiante['carrera']) ?></td>
                            <td data-titulo="Sem."><?= e($estudiante['semestre']) ?></td>
                            <td data-titulo="Registrado"><?= e(date('d/m/Y H:i', strtotime($estudiante['fecha_creacion']))) ?></td>
                        </tr>
<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
<?php endif; ?>
        </section>
<?php require __DIR__ . '/includes/footer.php'; ?>
