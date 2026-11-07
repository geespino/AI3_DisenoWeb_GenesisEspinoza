# AI3_DisenoWeb_GenesisEspinoza

**Sistema web de registro de estudiantes** desarrollado en PHP modular con base de datos MariaDB/MySQL.

- **Estudiante:** Genesis Espinoza
- **Asignatura:** Programación de Sistemas Web — Universidad ECOTEC
- **Actividad:** Actividad Integradora 3

## Funcionalidades

| Módulo | Archivo | Seguridad aplicada |
|---|---|---|
| Conexión | `config/conexion.php` | PDO con `try/catch`; los errores se guardan en `logs/app.log` y el usuario solo ve un mensaje genérico |
| Registro (Create) | `index.php` → `guardar.php` | Validación HTML5 en el cliente, validación en el servidor, token CSRF y **sentencias preparadas** (`prepare` + `bindValue` + `execute`) |
| Consulta (Read) | `listar.php` | Búsqueda con parámetros y salida escapada con `htmlspecialchars()` para prevenir XSS |

## Tecnologías

- PHP 8.1 o superior (probado con PHP 8.3 y 8.5) · extensión **PDO_MySQL**
- MariaDB 10.11 / MySQL 8 · HTML5 · CSS3
- XAMPP como entorno local

## Estructura del proyecto

```
AI3_DisenoWeb_GenesisEspinoza/
├── index.php               → formulario de registro
├── guardar.php             → validación e inserción con sentencias preparadas
├── listar.php              → tabla de estudiantes con salida sanitizada
├── config/
│   └── conexion.php        → conexión PDO segura (try/catch)
├── includes/
│   ├── funciones.php       → escape XSS, validaciones, token CSRF
│   ├── header.php
│   └── footer.php
├── assets/css/styles.css
├── database/
│   └── db_sistema_web.sql  → script DDL de la base de datos
├── logs/                   → registro de errores (ignorado por Git)
├── .gitignore
└── README.md
```

## Despliegue local con XAMPP

1. **Instalar XAMPP** desde https://www.apachefriends.org con los componentes Apache, MySQL, PHP y phpMyAdmin.
2. **Iniciar los servicios:** abrir *XAMPP Control Panel* y pulsar **Start** en **Apache** y **MySQL**.
3. **Copiar el proyecto** en la carpeta pública de XAMPP:
   ```
   C:\xampp\htdocs\AI3_DisenoWeb_GenesisEspinoza
   ```
   (o clonarlo: `git clone https://github.com/geespino/AI3_DisenoWeb_GenesisEspinoza.git` dentro de `C:\xampp\htdocs`).
4. **Crear la base de datos:** abrir http://localhost/phpmyadmin → pestaña **Importar** → seleccionar `database/db_sistema_web.sql` → **Importar**. Se crean la base `db_sistema_web` y las tablas `carreras` y `estudiantes`.
5. **Abrir el sistema:** http://localhost/AI3_DisenoWeb_GenesisEspinoza/

### Credenciales de la base de datos

Por defecto se usan las de XAMPP (`root` sin contraseña). Si tu MySQL tiene otra contraseña, crea el archivo `config/config.local.php` (no se sube a GitHub):

```php
<?php
return [
    'usuario' => 'root',
    'clave'   => 'tu_contraseña',
];
```

## Pruebas de seguridad realizadas

| Prueba | Entrada | Resultado |
|---|---|---|
| Inyección SQL en la búsqueda | `' OR '1'='1` | No devuelve registros: el texto se trata como dato |
| Apóstrofe en un apellido | `O'Neil` | Se guarda correctamente sin romper la consulta |
| XSS almacenado | `<script>alert(2)</script>` | Se muestra como texto (`&lt;script&gt;`), no se ejecuta |
| Formulario falsificado | Token CSRF inválido | El registro se rechaza |
| Base de datos caída | Contraseña incorrecta | Mensaje genérico; el detalle queda en `logs/app.log` |
