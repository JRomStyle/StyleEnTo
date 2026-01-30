-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS juguetes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE juguetes;

-- Crear tablas
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin','cliente') NOT NULL DEFAULT 'cliente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    edad_recomendada VARCHAR(50),
    edad_min INT NULL,
    edad_max INT NULL,
    imagen VARCHAR(255),
    genero ENUM('niño','niña','unisex') NOT NULL DEFAULT 'unisex',
    categoria_id INT NOT NULL,
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    CONSTRAINT fk_prod_cat FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente','pagado','enviado','cancelado') NOT NULL DEFAULT 'pendiente',
    CONSTRAINT fk_ped_user FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS detalle_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_det_ped FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_det_prod FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pedido_historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    estado ENUM('pendiente','pagado','enviado','cancelado') NOT NULL,
    usuario_admin_id INT NULL,
    nota VARCHAR(255) NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_hist_ped FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_hist_admin FOREIGN KEY (usuario_admin_id) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS inventario_movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    tipo ENUM('venta','entrada','ajuste') NOT NULL,
    cantidad INT NOT NULL,
    pedido_id INT NULL,
    usuario_admin_id INT NULL,
    nota VARCHAR(255) NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mov_prod FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_mov_ped FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_mov_admin FOREIGN KEY (usuario_admin_id) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar categorías
INSERT IGNORE INTO categorias (nombre, descripcion) VALUES
('Educativos', 'Juegos que estimulan el aprendizaje'),
('Muñecas', 'Muñecas de diferentes tipos y estilos'),
('Figuras de acción', 'Figuras articuladas de personajes populares'),
('Vehículos', 'Coches, trenes, aviones y más'),
('Juegos de mesa', 'Juegos para toda la familia'),
('Bebés', 'Juegos seguros para los más pequeños'),
('Tecnológicos', 'Juegos con componentes electrónicos'),
('Exterior', 'Juegos para disfrutar al aire libre');

-- Insertar usuario admin (contraseña: admin123)
INSERT IGNORE INTO usuarios (nombre, email, password, rol) VALUES
('Admin', 'admin@local.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insertar productos de ejemplo
INSERT IGNORE INTO productos (nombre, descripcion, precio, stock, edad_recomendada, edad_min, edad_max, imagen, genero, categoria_id, estado) VALUES
('Juego de bloques Montessori', 'Estimula creatividad y motricidad', 29.99, 80, '3-5', 3, 5, 'https://source.unsplash.com/1200x800/?montessori,blocks,toy&sig=1', 'unisex', 1, 'activo'),
('Robot programable Junior', 'Aprende lógica y programación', 89.90, 30, '9-12', 9, 12, 'https://source.unsplash.com/1200x800/?robot,toy,kids&sig=2', 'niño', 7, 'activo'),
('Muñeca clásica con accesorios', 'Incluye vestuario y peines', 24.50, 120, '6-8', 6, 8, 'https://source.unsplash.com/1200x800/?doll,toy,kids&sig=3', 'niña', 2, 'activo'),
('Figura de acción Héroe X', 'Articulada y resistente', 19.99, 150, '6-8', 6, 8, 'https://source.unsplash.com/1200x800/?action-figure,toy&sig=4', 'niño', 3, 'activo'),
('Auto a control remoto', 'Batería recargable y luces LED', 39.99, 60, '9-12', 9, 12, 'https://source.unsplash.com/1200x800/?remote-control,car,toy&sig=5', 'niño', 4, 'activo'),
('Tren de madera', 'Seguro y durable', 34.90, 50, '3-5', 3, 5, 'https://source.unsplash.com/1200x800/?wooden,train,toy&sig=6', 'unisex', 4, 'activo'),
('Ajedrez familiar', 'Juego clásico para pensar', 22.00, 90, '9-12', 9, 12, 'https://source.unsplash.com/1200x800/?chess,board-game&sig=7', 'unisex', 5, 'activo'),
('Memoria para peques', 'Parejas de animales', 15.90, 140, '3-5', 3, 5, 'https://source.unsplash.com/1200x800/?memory,card-game,kids&sig=8', 'unisex', 5, 'activo'),
('Sonajero suave bebé', 'Material hipoalergénico', 9.99, 200, '0-2', 0, 2, 'https://source.unsplash.com/1200x800/?baby,rattle,toy&sig=9', 'unisex', 6, 'activo'),
('Gimnasio de actividades', 'Colores y texturas', 49.50, 40, '0-2', 0, 2, 'https://source.unsplash.com/1200x800/?baby,play,mat,toy&sig=10', 'unisex', 6, 'activo'),
('Dron mini al aire libre', 'Estable y fácil de volar', 79.00, 25, '13+', 13, 99, 'https://source.unsplash.com/1200x800/?drone,quad,copter&sig=11', 'niño', 8, 'activo'),
('Pelota saltarina', 'Diversión y ejercicio', 12.00, 160, '6-8', 6, 8, 'https://source.unsplash.com/1200x800/?kids,ball,toy&sig=12', 'niña', 8, 'activo');
