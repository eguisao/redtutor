CREATE DATABASE IF NOT EXISTS redtutor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE redtutor;

CREATE TABLE IF NOT EXISTS usuarios (
  id_usuario INT NOT NULL AUTO_INCREMENT,
  correo VARCHAR(180) NOT NULL,
  fecha_creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_usuario),
  UNIQUE KEY uq_correo (correo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS materias (
  id_materia INT NOT NULL AUTO_INCREMENT,
  codigo VARCHAR(20) NOT NULL,
  nombre VARCHAR(120) NOT NULL,
  descripcion VARCHAR(255) DEFAULT NULL,
  area VARCHAR(80) DEFAULT NULL,
  activa TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id_materia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO materias (codigo, nombre, descripcion, area, activa) VALUES
  ('MAT-101', 'Matemáticas Básicas', 'Fundamentos de álgebra, aritmética y resolución de problemas.', 'Ciencias Exactas', 1),
  ('FIS-201', 'Física General', 'Conceptos básicos de movimiento, energía y fuerzas.', 'Ciencias Exactas', 1),
  ('QUI-102', 'Química General', 'Introducción a la química, enlaces y reacciones.', 'Ciencias', 1),
  ('BIO-103', 'Biología', 'Estudio de la vida, células, ecosistemas y genética.', 'Ciencias', 1),
  ('ESP-110', 'Español', 'Comprensión, redacción y análisis de textos.', 'Humanidades', 1),
  ('ING-205', 'Inglés', 'Práctica y mejora del inglés académico y cotidiano.', 'Idiomas', 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), descripcion = VALUES(descripcion), area = VALUES(area), activa = VALUES(activa);
