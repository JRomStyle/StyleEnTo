<?php
namespace App\Models;
use App\Database;
use PDO;
class Product {
    public static function allActive(): array {
        $pdo = Database::conn();
        $stmt = $pdo->query("SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id=c.id WHERE p.estado='activo' ORDER BY p.id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getActive(int $limit = 12): array {
        $pdo = Database::conn();
        $stmt = $pdo->prepare("SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id=c.id WHERE p.estado='activo' ORDER BY p.id DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function countActive(): int {
        $pdo = Database::conn();
        $stmt = $pdo->query("SELECT COUNT(*) AS c FROM productos WHERE estado='activo'");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['c'] ?? 0);
    }
    public static function paginateActive(int $page, int $perPage, string $sort): array {
        $order = 'p.id DESC';
        switch ($sort) {
            case 'name_asc': $order = 'p.nombre ASC'; break;
            case 'name_desc': $order = 'p.nombre DESC'; break;
            case 'price_asc': $order = 'p.precio ASC'; break;
            case 'price_desc': $order = 'p.precio DESC'; break;
            default: $order = 'p.id DESC';
        }
        $offset = max(0, ($page - 1) * $perPage);
        $pdo = Database::conn();
        $sql = "SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id=c.id WHERE p.estado='activo' ORDER BY $order LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function find(int $id): ?array {
        $pdo = Database::conn();
        $stmt = $pdo->prepare("SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id=c.id WHERE p.id=? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    public static function search(string $q): array {
        $pdo = Database::conn();
        $like = '%' . $q . '%';
        $stmt = $pdo->prepare("SELECT id,nombre,precio,imagen FROM productos WHERE estado='activo' AND (nombre LIKE ? OR descripcion LIKE ?) ORDER BY nombre LIMIT 50");
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function countActiveFiltered(array $filters): int {
        $pdo = Database::conn();
        [$where, $params] = self::buildFilterWhere($filters);
        $stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM productos p WHERE $where");
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['c'] ?? 0);
    }
    public static function paginateActiveFiltered(int $page, int $perPage, string $sort, array $filters): array {
        $order = 'p.id DESC';
        switch ($sort) {
            case 'name_asc': $order = 'p.nombre ASC'; break;
            case 'name_desc': $order = 'p.nombre DESC'; break;
            case 'price_asc': $order = 'p.precio ASC'; break;
            case 'price_desc': $order = 'p.precio DESC'; break;
            default: $order = 'p.id DESC';
        }
        $offset = max(0, ($page - 1) * $perPage);
        $pdo = Database::conn();
        [$where, $params] = self::buildFilterWhere($filters);
        $sql = "SELECT p.*, c.nombre AS categoria 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id=c.id 
                WHERE $where 
                ORDER BY $order 
                LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $i => $val) {
            $stmt->bindValue($i + 1, $val);
        }
        $stmt->bindValue(count($params) + 1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function create(array $data): bool {
        $pdo = Database::conn();
        if (self::hasColumn('genero') && self::hasColumn('edad_min') && self::hasColumn('edad_max')) {
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, edad_recomendada, edad_min, edad_max, imagen, genero, categoria_id, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $data['nombre'],
                $data['descripcion'],
                $data['precio'],
                $data['stock'],
                $data['edad_recomendada'],
                $data['edad_min'] ?? null,
                $data['edad_max'] ?? null,
                $data['imagen'],
                $data['genero'] ?? 'unisex',
                $data['categoria_id'],
                $data['estado']
            ]);
        }
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, edad_recomendada, imagen, categoria_id, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['stock'],
            $data['edad_recomendada'],
            $data['imagen'],
            $data['categoria_id'],
            $data['estado']
        ]);
    }
    public static function update(int $id, array $data): bool {
        $pdo = Database::conn();
        if (self::hasColumn('genero') && self::hasColumn('edad_min') && self::hasColumn('edad_max')) {
            $stmt = $pdo->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, edad_recomendada=?, edad_min=?, edad_max=?, imagen=?, genero=?, categoria_id=?, estado=? WHERE id=?");
            return $stmt->execute([
                $data['nombre'],
                $data['descripcion'],
                $data['precio'],
                $data['stock'],
                $data['edad_recomendada'],
                $data['edad_min'] ?? null,
                $data['edad_max'] ?? null,
                $data['imagen'],
                $data['genero'] ?? 'unisex',
                $data['categoria_id'],
                $data['estado'],
                $id
            ]);
        }
        $stmt = $pdo->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, edad_recomendada=?, imagen=?, categoria_id=?, estado=? WHERE id=?");
        return $stmt->execute([
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['stock'],
            $data['edad_recomendada'],
            $data['imagen'],
            $data['categoria_id'],
            $data['estado'],
            $id
        ]);
    }
    public static function delete(int $id): bool {
        $pdo = Database::conn();
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id=?");
        return $stmt->execute([$id]);
    }
    public static function decrementStock(int $id, int $qty): bool {
        $pdo = Database::conn();
        $stmt = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id=? AND stock >= ?");
        return $stmt->execute([$qty, $id, $qty]);
    }
    public static function logInventoryMovement(int $producto_id, string $tipo, int $cantidad, ?int $pedido_id = null, ?int $usuario_admin_id = null, ?string $nota = null): bool {
        $allowed = ['venta','entrada','ajuste'];
        if (!in_array($tipo, $allowed, true)) {
            return false;
        }
        $pdo = Database::conn();
        $stmt = $pdo->prepare("INSERT INTO inventario_movimientos (producto_id, tipo, cantidad, pedido_id, usuario_admin_id, nota) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$producto_id, $tipo, $cantidad, $pedido_id, $usuario_admin_id, $nota]);
    }
    public static function byCategory(int $categoria_id): array {
        $pdo = Database::conn();
        $stmt = $pdo->prepare("SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id=c.id WHERE p.categoria_id=? ORDER BY p.id DESC");
        $stmt->execute([$categoria_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function moveCategory(int $id, int $categoria_id): bool {
        $pdo = Database::conn();
        $stmt = $pdo->prepare("UPDATE productos SET categoria_id=? WHERE id=?");
        return $stmt->execute([$categoria_id, $id]);
    }
    private static function buildFilterWhere(array $filters): array {
        $where = ["p.estado='activo'"];
        $params = [];
        if (!empty($filters['cat'])) {
            $where[] = "p.categoria_id=?";
            $params[] = (int)$filters['cat'];
        }
        if (!empty($filters['gender']) && self::hasColumn('genero')) {
            $where[] = "p.genero=?";
            $params[] = $filters['gender'];
        }
        if (!empty($filters['q'])) {
            $where[] = "(p.nombre LIKE ? OR p.descripcion LIKE ?)";
            $like = '%' . $filters['q'] . '%';
            $params[] = $like;
            $params[] = $like;
        }
        if (!empty($filters['price_min']) || !empty($filters['price_max'])) {
            if (isset($filters['price_min'])) {
                $where[] = "p.precio >= ?";
                $params[] = (float)$filters['price_min'];
            }
            if (isset($filters['price_max'])) {
                $where[] = "p.precio <= ?";
                $params[] = (float)$filters['price_max'];
            }
        }
        if ((isset($filters['age_min']) || isset($filters['age_max'])) && self::hasColumn('edad_min') && self::hasColumn('edad_max')) {
            $ageMin = isset($filters['age_min']) ? (int)$filters['age_min'] : null;
            $ageMax = isset($filters['age_max']) ? (int)$filters['age_max'] : null;
            if ($ageMin !== null && $ageMax !== null) {
                $where[] = "(p.edad_min IS NULL OR p.edad_min <= ?) AND (p.edad_max IS NULL OR p.edad_max >= ?)";
                $params[] = $ageMax;
                $params[] = $ageMin;
            }
        }
        return [implode(' AND ', $where), $params];
    }
    private static function hasColumn(string $name): bool {
        static $columns = null;
        if ($columns === null) {
            $pdo = Database::conn();
            $row = $pdo->query("SELECT DATABASE() AS d")->fetch(PDO::FETCH_ASSOC);
            $dbName = $row['d'] ?? '';
            $columns = [];
            if ($dbName !== '') {
                $stmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME='productos'");
                $stmt->execute([$dbName]);
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
                    $columns[$col['COLUMN_NAME']] = true;
                }
            }
        }
        return isset($columns[$name]);
    }
}
