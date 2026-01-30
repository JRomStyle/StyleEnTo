CREATE DATABASE IF NOT EXISTS style_en_to
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE style_en_to;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('cliente','admin') NOT NULL DEFAULT 'cliente',
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS productos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(160) NOT NULL,
  slug VARCHAR(180) NOT NULL,
  descripcion TEXT NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  categoria ENUM('Hombre','Mujer','Unisex') NOT NULL,
  tipo VARCHAR(80) NOT NULL,
  tallas_csv VARCHAR(120) NOT NULL,
  imagen_url VARCHAR(500) NOT NULL,
  ventas INT UNSIGNED NOT NULL DEFAULT 0,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_productos_slug (slug),
  KEY idx_productos_categoria (categoria),
  KEY idx_productos_tipo (tipo),
  KEY idx_productos_precio (precio),
  KEY idx_productos_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS carrito (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id INT UNSIGNED NOT NULL,
  producto_id INT UNSIGNED NOT NULL,
  talla VARCHAR(20) NOT NULL,
  cantidad INT UNSIGNED NOT NULL DEFAULT 1,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_carrito_user_producto_talla (usuario_id, producto_id, talla),
  KEY idx_carrito_usuario (usuario_id),
  KEY idx_carrito_producto (producto_id),
  CONSTRAINT fk_carrito_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_carrito_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pedidos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id INT UNSIGNED NULL,
  email VARCHAR(190) NOT NULL,
  nombre VARCHAR(120) NOT NULL,
  direccion VARCHAR(220) NOT NULL,
  ciudad VARCHAR(120) NOT NULL,
  pais VARCHAR(120) NOT NULL,
  telefono VARCHAR(40) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  estado ENUM('pendiente','pagado','enviado','cancelado') NOT NULL DEFAULT 'pendiente',
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_pedidos_usuario (usuario_id),
  KEY idx_pedidos_estado (estado),
  CONSTRAINT fk_pedidos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pedido_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  pedido_id BIGINT UNSIGNED NOT NULL,
  producto_id INT UNSIGNED NOT NULL,
  nombre VARCHAR(160) NOT NULL,
  talla VARCHAR(20) NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  cantidad INT UNSIGNED NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_pedido_items_pedido (pedido_id),
  KEY idx_pedido_items_producto (producto_id),
  CONSTRAINT fk_pedido_items_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
  CONSTRAINT fk_pedido_items_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO usuarios (nombre, email, password_hash, rol)
VALUES ('jromstyle', 'jrom@styleento.local', 'jrom123', 'admin')
ON DUPLICATE KEY UPDATE rol = VALUES(rol);

INSERT INTO productos (nombre, slug, descripcion, precio, categoria, tipo, tallas_csv, imagen_url, ventas, activo)
VALUES
('Hoodie Neon Pulse', 'hoodie-neon-pulse', 'Hoodie premium con interior suave, fit moderno y detalles neón. Diseñada para moverte con actitud.', 59.90, 'Unisex', 'hoodies', 'XS,S,M,L,XL', 'hoodie-neon-pulse.jpg', 34, 1),
('Camiseta Essential Mono', 'camiseta-essential-mono', 'Camiseta minimalista de algodón peinado. Caída limpia, cuello reforzado y look premium.', 24.90, 'Unisex', 'camisas', 'XS,S,M,L,XL', 'camiseta-essential-mono.jpg', 68, 1),
('Tenis Aero Street', 'tenis-aero-street', 'Tenis ultraligeros con amortiguación diaria y diseño inspirado en performance urbano.', 89.00, 'Hombre', 'tenis', '39,40,41,42,43,44', 'Tenis-Aero-Street.jpg', 51, 1),
('Gorra Shadow Cap', 'gorra-shadow-cap', 'Gorra clásica con estructura ligera, bordado limpio y ajuste cómodo.', 18.50, 'Unisex', 'gorras', 'U', 'gorra-shadow-cap.jpg', 27, 1),
('Jogger Core Flex', 'jogger-core-flex', 'Jogger de tejido técnico con elasticidad y corte tapered. Ideal para calle o entrenamiento.', 44.90, 'Mujer', 'pantalones', 'XS,S,M,L,XL', 'jogger-core-flex.jpg', 22, 1),
('Chaqueta Wind Pro', 'chaqueta-wind-pro', 'Chaqueta ligera anti-viento con acabado mate y líneas minimalistas.', 79.90, 'Hombre', 'chaquetas', 'S,M,L,XL', 'chaqueta-wind-pro.jpg', 12, 1)
ON DUPLICATE KEY UPDATE actualizado_en = CURRENT_TIMESTAMP;

