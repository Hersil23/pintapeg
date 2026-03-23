-- =============================================
-- PintaPeg Ecommerce - Database Schema
-- =============================================

CREATE DATABASE IF NOT EXISTS pintapeg
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE pintapeg;

-- =============================================
-- Tabla: usuarios
-- =============================================
CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('admin', 'editor') NOT NULL DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- Tabla: categorias
-- =============================================
CREATE TABLE IF NOT EXISTS categorias (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  imagen VARCHAR(255) DEFAULT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  orden INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- Tabla: productos
-- =============================================
CREATE TABLE IF NOT EXISTS productos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT UNSIGNED NOT NULL,
  nombre VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  descripcion TEXT DEFAULT NULL,
  precio DECIMAL(10,2) NOT NULL,
  moneda_base ENUM('usd', 'ves') NOT NULL DEFAULT 'usd',
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  imagen VARCHAR(255) DEFAULT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  destacado TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_producto_categoria
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- Tabla: tasa_dolar
-- =============================================
CREATE TABLE IF NOT EXISTS tasa_dolar (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tipo ENUM('bcv', 'paralelo') NOT NULL,
  valor DECIMAL(10,4) NOT NULL,
  fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_tipo (tipo)
) ENGINE=InnoDB;

-- =============================================
-- Tabla: ventas
-- =============================================
CREATE TABLE IF NOT EXISTS ventas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  referencia VARCHAR(50) NOT NULL UNIQUE,
  productos_json JSON NOT NULL,
  total_usd DECIMAL(10,2) NOT NULL,
  total_ves DECIMAL(12,2) NOT NULL,
  tasa_usada DECIMAL(10,4) NOT NULL,
  moneda_cliente ENUM('usd', 'ves') NOT NULL,
  nombre_cliente VARCHAR(150) NOT NULL,
  direccion TEXT NOT NULL,
  referencia_entrega VARCHAR(255) DEFAULT NULL,
  fecha DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- Tabla: config_tienda
-- =============================================
CREATE TABLE IF NOT EXISTS config_tienda (
  clave VARCHAR(50) PRIMARY KEY,
  valor VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- =============================================
-- Datos iniciales de configuracion
-- =============================================
INSERT INTO config_tienda (clave, valor) VALUES
  ('tasa_activa', 'bcv'),
  ('moneda_default', 'usd'),
  ('whatsapp', '04265196026')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

-- =============================================
-- Tasas iniciales (se actualizan via API)
-- =============================================
INSERT INTO tasa_dolar (tipo, valor) VALUES
  ('bcv', 0.0000),
  ('paralelo', 0.0000)
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

-- =============================================
-- Usuario admin inicial
-- Password: cambiar en primer login
-- hash de 'PintaPeg2024!' generado con password_hash()
-- =============================================
-- NOTA: Ejecutar este INSERT desde PHP usando password_hash()
-- para generar el hash correcto:
--
-- INSERT INTO usuarios (nombre, email, password_hash, rol)
-- VALUES ('Admin', 'mpintapeg@gmail.com', '$hash_generado', 'admin');
