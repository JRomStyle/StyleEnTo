<?php
namespace App\Controllers;
use App\Controllers\Controller;
use App\Database;
use App\Models\User;
class SetupController extends Controller {
    public function install(): void {
        $pdo = Database::conn();
        $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            rol ENUM('admin','cliente') NOT NULL DEFAULT 'cliente',
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $pdo->exec("CREATE TABLE IF NOT EXISTS categorias (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            descripcion TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $pdo->exec("CREATE TABLE IF NOT EXISTS productos (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $dbName = $pdo->query("SELECT DATABASE() as d")->fetch()['d'] ?? 'juguetes';
        $col = $pdo->prepare("SELECT COUNT(*) c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME='productos' AND COLUMN_NAME=?");
        $col->execute([$dbName, 'genero']);
        $hasGenero = (int)($col->fetch()['c'] ?? 0) > 0;
        if (!$hasGenero) {
            $pdo->exec("ALTER TABLE productos ADD COLUMN genero ENUM('niño','niña','unisex') NOT NULL DEFAULT 'unisex' AFTER imagen");
        }
        $col->execute([$dbName, 'edad_min']);
        $hasEdadMin = (int)($col->fetch()['c'] ?? 0) > 0;
        if (!$hasEdadMin) {
            $pdo->exec("ALTER TABLE productos ADD COLUMN edad_min INT NULL AFTER edad_recomendada");
        }
        $col->execute([$dbName, 'edad_max']);
        $hasEdadMax = (int)($col->fetch()['c'] ?? 0) > 0;
        if (!$hasEdadMax) {
            $pdo->exec("ALTER TABLE productos ADD COLUMN edad_max INT NULL AFTER edad_min");
        }
        $pdo->exec("UPDATE productos SET 
            edad_min = CASE 
                WHEN edad_recomendada LIKE '%-%' THEN CAST(SUBSTRING_INDEX(edad_recomendada,'-',1) AS UNSIGNED)
                WHEN edad_recomendada LIKE '%+%' THEN CAST(REPLACE(edad_recomendada,'+','') AS UNSIGNED)
                ELSE NULL
            END,
            edad_max = CASE
                WHEN edad_recomendada LIKE '%-%' THEN CAST(SUBSTRING_INDEX(edad_recomendada,'-',-1) AS UNSIGNED)
                WHEN edad_recomendada LIKE '%+%' THEN 99
                ELSE NULL
            END
            WHERE (edad_min IS NULL OR edad_max IS NULL) AND (edad_recomendada IS NOT NULL AND edad_recomendada <> '')");
        $pdo->exec("CREATE TABLE IF NOT EXISTS pedidos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            total DECIMAL(10,2) NOT NULL,
            fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            estado ENUM('pendiente','pagado','enviado','cancelado') NOT NULL DEFAULT 'pendiente',
            CONSTRAINT fk_ped_user FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $pdo->exec("CREATE TABLE IF NOT EXISTS detalle_pedido (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pedido_id INT NOT NULL,
            producto_id INT NOT NULL,
            cantidad INT NOT NULL,
            precio_unitario DECIMAL(10,2) NOT NULL,
            CONSTRAINT fk_det_ped FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_det_prod FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $exists = $pdo->query("SELECT COUNT(*) c FROM categorias")->fetch()['c'] ?? 0;
        if ((int)$exists === 0) {
            $cats = ['Educativos','Muñecas','Figuras de acción','Vehículos','Juegos de mesa','Bebés','Tecnológicos','Exterior'];
            $stmt = $pdo->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, '')");
            foreach ($cats as $n) $stmt->execute([$n]);
        }
        $pcount = $pdo->query("SELECT COUNT(*) c FROM productos")->fetch()['c'] ?? 0;
        if ((int)$pcount === 0) {
            $map = [];
            foreach ($pdo->query("SELECT id,nombre FROM categorias") as $row) {
                $map[$row['nombre']] = (int)$row['id'];
            }
            $items = [
                ['Juego de bloques Montessori','Estimula creatividad y motricidad','29.99',80,'3-5',3,5,'https://placehold.co/600x400/png?text=Bloques%20Montessori','unisex','Educativos','activo'],
                ['Robot programable Junior','Aprende lógica y programación','89.90',30,'9-12',9,12,'https://placehold.co/600x400/png?text=Robot%20Junior','niño','Tecnológicos','activo'],
                ['Muñeca clásica con accesorios','Incluye vestuario y peines','24.50',120,'6-8',6,8,'https://placehold.co/600x400/png?text=Muneca%20Clasica','niña','Muñecas','activo'],
                ['Figura de acción Héroe X','Articulada y resistente','19.99',150,'6-8',6,8,'https://placehold.co/600x400/png?text=Heroe%20X','niño','Figuras de acción','activo'],
                ['Auto a control remoto','Batería recargable y luces LED','39.99',60,'9-12',9,12,'https://placehold.co/600x400/png?text=Auto%20Control%20Remoto','niño','Vehículos','activo'],
                ['Tren de madera','Seguro y durable','34.90',50,'3-5',3,5,'https://placehold.co/600x400/png?text=Tren%20de%20Madera','unisex','Vehículos','activo'],
                ['Ajedrez familiar','Juego clásico para pensar','22.00',90,'9-12',9,12,'https://placehold.co/600x400/png?text=Ajedrez%20Familiar','unisex','Juegos de mesa','activo'],
                ['Memoria para peques','Parejas de animales','15.90',140,'3-5',3,5,'https://placehold.co/600x400/png?text=Juego%20de%20Memoria','unisex','Juegos de mesa','activo'],
                ['Sonajero suave bebé','Material hipoalergénico','9.99',200,'0-2',0,2,'https://placehold.co/600x400/png?text=Sonajero%20Bebe','unisex','Bebés','activo'],
                ['Gimnasio de actividades','Colores y texturas','49.50',40,'0-2',0,2,'https://placehold.co/600x400/png?text=Gimnasio%20Bebe','unisex','Bebés','activo'],
                ['Dron mini al aire libre','Estable y fácil de volar','79.00',25,'13+',13,99,'https://placehold.co/600x400/png?text=Dron%20Mini','niño','Exterior','activo'],
                ['Pelota saltarina','Diversión y ejercicio','12.00',160,'6-8',6,8,'https://placehold.co/600x400/png?text=Pelota%20Saltarina','niña','Exterior','activo'],
            ];
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, edad_recomendada, edad_min, edad_max, imagen, genero, categoria_id, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($items as $it) {
                $catId = $map[$it[9]] ?? null;
                if ($catId) {
                    $stmt->execute([$it[0], $it[1], $it[2], $it[3], $it[4], $it[5], $it[6], $it[7], $it[8], $catId, $it[10]]);
                }
            }
        }
        $admin = User::findByEmail('admin@local.test');
        if (!$admin) {
            User::create('Admin', 'admin@local.test', 'admin123', 'admin');
        }
        $this->render('setup/install', []);
    }
    public function seed(): void {
        $pdo = Database::conn();
        $cats = ['Educativos','Muñecas','Figuras de acción','Vehículos','Juegos de mesa','Bebés','Tecnológicos','Exterior'];
        foreach ($cats as $n) {
            $stmt = $pdo->prepare("SELECT id FROM categorias WHERE nombre=? LIMIT 1");
            $stmt->execute([$n]);
            if (!$stmt->fetch()) {
                $pdo->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, '')")->execute([$n]);
            }
        }
        $map = [];
        foreach ($pdo->query("SELECT id,nombre FROM categorias") as $row) {
            $map[$row['nombre']] = (int)$row['id'];
        }
        $items = [
            ['Juego de bloques Montessori','Estimula creatividad y motricidad','29.99',80,'3-5',3,5,'https://placehold.co/600x400/png?text=Bloques%20Montessori','unisex','Educativos','activo'],
            ['Robot programable Junior','Aprende lógica y programación','89.90',30,'9-12',9,12,'https://placehold.co/600x400/png?text=Robot%20Junior','niño','Tecnológicos','activo'],
            ['Muñeca clásica con accesorios','Incluye vestuario y peines','24.50',120,'6-8',6,8,'https://placehold.co/600x400/png?text=Muneca%20Clasica','niña','Muñecas','activo'],
            ['Figura de acción Héroe X','Articulada y resistente','19.99',150,'6-8',6,8,'https://placehold.co/600x400/png?text=Heroe%20X','niño','Figuras de acción','activo'],
            ['Auto a control remoto','Batería recargable y luces LED','39.99',60,'9-12',9,12,'https://placehold.co/600x400/png?text=Auto%20Control%20Remoto','niño','Vehículos','activo'],
            ['Tren de madera','Seguro y durable','34.90',50,'3-5',3,5,'https://placehold.co/600x400/png?text=Tren%20de%20Madera','unisex','Vehículos','activo'],
            ['Ajedrez familiar','Juego clásico para pensar','22.00',90,'9-12',9,12,'https://placehold.co/600x400/png?text=Ajedrez%20Familiar','unisex','Juegos de mesa','activo'],
            ['Memoria para peques','Parejas de animales','15.90',140,'3-5',3,5,'https://placehold.co/600x400/png?text=Juego%20de%20Memoria','unisex','Juegos de mesa','activo'],
            ['Sonajero suave bebé','Material hipoalergénico','9.99',200,'0-2',0,2,'https://placehold.co/600x400/png?text=Sonajero%20Bebe','unisex','Bebés','activo'],
            ['Gimnasio de actividades','Colores y texturas','49.50',40,'0-2',0,2,'https://placehold.co/600x400/png?text=Gimnasio%20Bebe','unisex','Bebés','activo'],
            ['Dron mini al aire libre','Estable y fácil de volar','79.00',25,'13+',13,99,'https://placehold.co/600x400/png?text=Dron%20Mini','niño','Exterior','activo'],
            ['Pelota saltarina','Diversión y ejercicio','12.00',160,'6-8',6,8,'https://placehold.co/600x400/png?text=Pelota%20Saltarina','niña','Exterior','activo'],
        ];
        $find = $pdo->prepare("SELECT id FROM productos WHERE nombre=? LIMIT 1");
        $insert = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, edad_recomendada, edad_min, edad_max, imagen, genero, categoria_id, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($items as $it) {
            $find->execute([$it[0]]);
            if (!$find->fetch()) {
                $catId = $map[$it[9]] ?? null;
                if ($catId) {
                    $insert->execute([$it[0], $it[1], $it[2], $it[3], $it[4], $it[5], $it[6], $it[7], $it[8], $catId, $it[10]]);
                }
            }
        }
        $this->render('setup/install', []);
    }
}
