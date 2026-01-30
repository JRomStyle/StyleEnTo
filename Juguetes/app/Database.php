<?php
namespace App;
use PDO;
use PDOException;
class Database {
    private static ?PDO $pdo = null;
    private static bool $schemaChecked = false;
    public static function conn(): PDO {
        if (!self::$pdo) {
            $config = require __DIR__ . '/Config/config.php';
            $host = $config['db']['host'];
            $dbName = $config['db']['name'];
            $user = $config['db']['user'];
            $pass = $config['db']['pass'];
            try {
                $dsn = 'mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8mb4';
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                $msg = $e->getMessage();
                if (stripos($msg, 'Unknown database') !== false || (string)$e->getCode() === '1049') {
                    $dsn = 'mysql:host=' . $host . ';charset=utf8mb4';
                    $pdo = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                    $safeDb = '`' . str_replace('`', '``', $dbName) . '`';
                    $pdo->exec("CREATE DATABASE IF NOT EXISTS $safeDb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8mb4';
                    self::$pdo = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                } else {
                    throw $e;
                }
            }
        }
        self::ensureSchema();
        return self::$pdo;
    }

    private static function ensureSchema(): void {
        if (self::$schemaChecked || !self::$pdo) return;
        self::$schemaChecked = true;
        $pdo = self::$pdo;

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

        $row = $pdo->query("SELECT DATABASE() AS d")->fetch(PDO::FETCH_ASSOC);
        $dbName = $row['d'] ?? '';
        if ($dbName !== '') {
            $col = $pdo->prepare("SELECT COUNT(*) c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME='productos' AND COLUMN_NAME=?");
            $col->execute([$dbName, 'genero']);
            $hasGenero = (int)($col->fetch(PDO::FETCH_ASSOC)['c'] ?? 0) > 0;
            if (!$hasGenero) {
                $pdo->exec("ALTER TABLE productos ADD COLUMN genero ENUM('niño','niña','unisex') NOT NULL DEFAULT 'unisex' AFTER imagen");
            }

            $col->execute([$dbName, 'edad_min']);
            $hasEdadMin = (int)($col->fetch(PDO::FETCH_ASSOC)['c'] ?? 0) > 0;
            if (!$hasEdadMin) {
                $pdo->exec("ALTER TABLE productos ADD COLUMN edad_min INT NULL AFTER edad_recomendada");
            }

            $col->execute([$dbName, 'edad_max']);
            $hasEdadMax = (int)($col->fetch(PDO::FETCH_ASSOC)['c'] ?? 0) > 0;
            if (!$hasEdadMax) {
                $pdo->exec("ALTER TABLE productos ADD COLUMN edad_max INT NULL AFTER edad_min");
            }
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

        $pdo->exec("CREATE TABLE IF NOT EXISTS pedido_historial (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pedido_id INT NOT NULL,
            estado ENUM('pendiente','pagado','enviado','cancelado') NOT NULL,
            usuario_admin_id INT NULL,
            nota VARCHAR(255) NULL,
            fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_hist_ped FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_hist_admin FOREIGN KEY (usuario_admin_id) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS inventario_movimientos (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $exists = $pdo->query("SELECT COUNT(*) c FROM categorias")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0;
        if ((int)$exists === 0) {
            $cats = ['Educativos','Muñecas','Figuras de acción','Vehículos','Juegos de mesa','Bebés','Tecnológicos','Exterior'];
            $stmt = $pdo->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, '')");
            foreach ($cats as $n) $stmt->execute([$n]);
        }

        $pcount = 0;
        try {
            $pcount = (int)($pdo->query("SELECT COUNT(*) c FROM productos WHERE estado='activo'")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);
        } catch (PDOException $e) {
            $pcount = (int)($pdo->query("SELECT COUNT(*) c FROM productos")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);
        }
        if ($pcount === 0) {
            $map = [];
            foreach ($pdo->query("SELECT id,nombre FROM categorias") as $row) {
                $map[$row['nombre']] = (int)$row['id'];
            }
            $items = [
                ['Juego de bloques Montessori','Estimula creatividad y motricidad','29.99',80,'3-5',3,5,'https://source.unsplash.com/1200x800/?montessori,blocks,toy&sig=1','unisex','Educativos','activo'],
                ['Robot programable Junior','Aprende lógica y programación','89.90',30,'9-12',9,12,'https://source.unsplash.com/1200x800/?robot,toy,kids&sig=2','niño','Tecnológicos','activo'],
                ['Muñeca clásica con accesorios','Incluye vestuario y peines','24.50',120,'6-8',6,8,'https://source.unsplash.com/1200x800/?doll,toy,kids&sig=3','niña','Muñecas','activo'],
                ['Figura de acción Héroe X','Articulada y resistente','19.99',150,'6-8',6,8,'https://source.unsplash.com/1200x800/?action-figure,toy&sig=4','niño','Figuras de acción','activo'],
                ['Auto a control remoto','Batería recargable y luces LED','39.99',60,'9-12',9,12,'https://source.unsplash.com/1200x800/?remote-control,car,toy&sig=5','niño','Vehículos','activo'],
                ['Tren de madera','Seguro y durable','34.90',50,'3-5',3,5,'https://source.unsplash.com/1200x800/?wooden,train,toy&sig=6','unisex','Vehículos','activo'],
                ['Ajedrez familiar','Juego clásico para pensar','22.00',90,'9-12',9,12,'https://source.unsplash.com/1200x800/?chess,board-game&sig=7','unisex','Juegos de mesa','activo'],
                ['Memoria para peques','Parejas de animales','15.90',140,'3-5',3,5,'https://source.unsplash.com/1200x800/?memory,card-game,kids&sig=8','unisex','Juegos de mesa','activo'],
                ['Sonajero suave bebé','Material hipoalergénico','9.99',200,'0-2',0,2,'https://source.unsplash.com/1200x800/?baby,rattle,toy&sig=9','unisex','Bebés','activo'],
                ['Gimnasio de actividades','Colores y texturas','49.50',40,'0-2',0,2,'https://source.unsplash.com/1200x800/?baby,play,mat,toy&sig=10','unisex','Bebés','activo'],
                ['Dron mini al aire libre','Estable y fácil de volar','79.00',25,'13+',13,99,'https://source.unsplash.com/1200x800/?drone,quad,copter&sig=11','niño','Exterior','activo'],
                ['Pelota saltarina','Diversión y ejercicio','12.00',160,'6-8',6,8,'https://source.unsplash.com/1200x800/?kids,ball,toy&sig=12','niña','Exterior','activo'],
            ];
            $insert = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, edad_recomendada, edad_min, edad_max, imagen, genero, categoria_id, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($items as $it) {
                $catId = $map[$it[9]] ?? null;
                if ($catId) {
                    $insert->execute([$it[0], $it[1], $it[2], $it[3], $it[4], $it[5], $it[6], $it[7], $it[8], $catId, $it[10]]);
                }
            }
        }
        $imageMap = [
            'Juego de bloques Montessori' => 'https://source.unsplash.com/1200x800/?montessori,blocks,toy&sig=1',
            'Robot programable Junior' => 'https://source.unsplash.com/1200x800/?robot,toy,kids&sig=2',
            'Muñeca clásica con accesorios' => 'https://source.unsplash.com/1200x800/?doll,toy,kids&sig=3',
            'Figura de acción Héroe X' => 'https://source.unsplash.com/1200x800/?action-figure,toy&sig=4',
            'Auto a control remoto' => 'https://source.unsplash.com/1200x800/?remote-control,car,toy&sig=5',
            'Tren de madera' => 'https://source.unsplash.com/1200x800/?wooden,train,toy&sig=6',
            'Ajedrez familiar' => 'https://source.unsplash.com/1200x800/?chess,board-game&sig=7',
            'Memoria para peques' => 'https://source.unsplash.com/1200x800/?memory,card-game,kids&sig=8',
            'Sonajero suave bebé' => 'https://source.unsplash.com/1200x800/?baby,rattle,toy&sig=9',
            'Gimnasio de actividades' => 'https://source.unsplash.com/1200x800/?baby,play,mat,toy&sig=10',
            'Dron mini al aire libre' => 'https://source.unsplash.com/1200x800/?drone,quad,copter&sig=11',
            'Pelota saltarina' => 'https://source.unsplash.com/1200x800/?kids,ball,toy&sig=12',
        ];
        $imgUpd = $pdo->prepare("UPDATE productos SET imagen=? WHERE (imagen IS NULL OR imagen='' OR imagen LIKE '%placehold.co%' OR imagen LIKE '%via.placeholder%') AND nombre=?");
        foreach ($imageMap as $name => $url) {
            $imgUpd->execute([$url, $name]);
        }

        $admin = $pdo->prepare("SELECT id FROM usuarios WHERE email=? LIMIT 1");
        $admin->execute(['admin@local.test']);
        if (!$admin->fetch(PDO::FETCH_ASSOC)) {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'admin')")->execute(['Admin', 'admin@local.test', $hash]);
        }
    }
}
