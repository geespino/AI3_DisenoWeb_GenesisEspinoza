-- ==========================================================
-- Base de datos: db_sistema_web
-- Proyecto: AI3 - Registro de estudiantes (PHP + MariaDB)
-- Autora: Genesis Espinoza
-- Motor: MariaDB 10.11 / MySQL 8 (XAMPP)
-- ==========================================================

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS db_sistema_web
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE db_sistema_web;

-- ----------------------------------------------------------
-- Tabla catálogo: carreras
-- Se separa de estudiantes para cumplir la tercera forma normal
-- (el nombre de la carrera no se repite en cada estudiante).
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS carreras (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre         VARCHAR(100) NOT NULL,
    fecha_creacion DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_carreras_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- Tabla principal: estudiantes
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS estudiantes (
    id                  INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    cedula              CHAR(10)         NOT NULL,
    nombres             VARCHAR(80)      NOT NULL,
    apellidos           VARCHAR(80)      NOT NULL,
    correo              VARCHAR(120)     NOT NULL,
    telefono            VARCHAR(15)      NULL,
    carrera_id          INT UNSIGNED     NOT NULL,
    semestre            TINYINT UNSIGNED NOT NULL,
    fecha_creacion      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_estudiantes_cedula (cedula),
    UNIQUE KEY uq_estudiantes_correo (correo),
    KEY idx_estudiantes_apellidos (apellidos),
    CONSTRAINT fk_estudiantes_carrera
        FOREIGN KEY (carrera_id) REFERENCES carreras (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_estudiantes_semestre CHECK (semestre BETWEEN 1 AND 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- Datos iniciales del catálogo de carreras
-- ----------------------------------------------------------
INSERT IGNORE INTO carreras (nombre) VALUES
    ('Ingeniería en Sistemas Inteligentes'),
    ('Ingeniería en Ciberseguridad'),
    ('Ingeniería en Ciencia de Datos'),
    ('Ingeniería Industrial'),
    ('Administración de Empresas'),
    ('Contabilidad y Auditoría');
