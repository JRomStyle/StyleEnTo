CREATE DATABASE IF NOT EXISTS contagestor_dian DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE contagestor_dian;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  correo VARCHAR(120) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  rol ENUM('admin','asistente') NOT NULL
);

CREATE TABLE IF NOT EXISTS clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tipo ENUM('natural','juridico') NOT NULL,
  nombre VARCHAR(160) NOT NULL,
  documento VARCHAR(50) NOT NULL,
  correo VARCHAR(120) NULL,
  telefono VARCHAR(50) NULL,
  direccion VARCHAR(255) NULL,
  regimen ENUM('simplificado','responsable_iva') NULL
);

CREATE TABLE IF NOT EXISTS documentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  tipo_documento ENUM('renta','iva','retencion','camara','estados') NOT NULL,
  archivo VARCHAR(255) NOT NULL,
  fecha_subida DATETIME NOT NULL,
  estado ENUM('pendiente','revisado') NOT NULL DEFAULT 'pendiente',
  CONSTRAINT fk_documentos_clientes FOREIGN KEY (cliente_id)
    REFERENCES clientes (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS vencimientos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  descripcion VARCHAR(255) NOT NULL,
  fecha_limite DATE NOT NULL,
  estado ENUM('pendiente','pagado') NOT NULL DEFAULT 'pendiente',
  CONSTRAINT fk_vencimientos_clientes FOREIGN KEY (cliente_id)
    REFERENCES clientes (id) ON DELETE CASCADE
);

