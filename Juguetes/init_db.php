<?php
// Archivo para inicializar la base de datos
require __DIR__ . '/app/bootstrap.php';

use App\Database;

echo "Inicializando base de datos...\n";

// Al conectarse a la base de datos, se ejecuta automáticamente ensureSchema()
// que crea la base de datos, tablas y datos iniciales
$db = Database::conn();

echo "Base de datos inicializada correctamente!\n";
echo "\nDetalles:\n";
echo "- Nombre de la base de datos: juguetes\n";
echo "- Tablas creadas: usuarios, categorias, productos, pedidos, detalle_pedido, pedido_historial, inventario_movimientos\n";
echo "- Datos iniciales insertados (categorías, productos, usuario admin)\n";
echo "\nUsuario admin creado:\n";
echo "- Email: admin@local.test\n";
echo "- Contraseña: admin123\n";
