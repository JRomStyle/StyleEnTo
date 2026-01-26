CREATE DATABASE IF NOT EXISTS pos_fastfood CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pos_fastfood;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id)
) ENGINE=InnoDB;

CREATE TABLE branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    address VARCHAR(180) NOT NULL,
    city VARCHAR(80) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    role_id INT NOT NULL,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(160) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB;

CREATE TABLE ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    unit VARCHAR(20) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE inventory (
    ingredient_id INT PRIMARY KEY,
    quantity DECIMAL(10,2) NOT NULL DEFAULT 0,
    min_stock DECIMAL(10,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
) ENGINE=InnoDB;

CREATE TABLE recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    name VARCHAR(120) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE recipe_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
) ENGINE=InnoDB;

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    phone VARCHAR(40) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    user_id INT NOT NULL,
    order_type VARCHAR(20) NOT NULL,
    status VARCHAR(20) NOT NULL,
    table_id INT NULL,
    customer_id INT NULL,
    notes TEXT,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE deliveries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    customer_id INT NOT NULL,
    address VARCHAR(200) NOT NULL,
    status VARCHAR(30) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id)
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    extras TEXT,
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    method VARCHAR(30) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB;

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    phone VARCHAR(40),
    email VARCHAR(120)
) ENGINE=InnoDB;

CREATE TABLE purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
) ENGINE=InnoDB;

CREATE TABLE stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ingredient_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    reason VARCHAR(120) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
) ENGINE=InnoDB;

CREATE TABLE cash_registers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    user_id INT NOT NULL,
    opened_at DATETIME NOT NULL,
    closed_at DATETIME NULL,
    opening_amount DECIMAL(10,2) NOT NULL,
    closing_amount DECIMAL(10,2) DEFAULT NULL,
    difference DECIMAL(10,2) DEFAULT NULL,
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE `tables` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    name VARCHAR(60) NOT NULL,
    status VARCHAR(20) NOT NULL,
    FOREIGN KEY (branch_id) REFERENCES branches(id)
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(120) NOT NULL,
    user_id INT NULL,
    ip_address VARCHAR(60) NOT NULL,
    session_id VARCHAR(120) NOT NULL,
    metadata JSON NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_branch ON orders(branch_id);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_inventory_min ON inventory(min_stock);

INSERT INTO roles (name) VALUES
('Super Administrador'),
('Administrador'),
('Cajero'),
('Mesero'),
('Cocina'),
('Repartidor'),
('Auditor');

INSERT INTO permissions (key_name) VALUES
('pos.view'),
('pos.create'),
('kitchen.view'),
('kitchen.update'),
('inventory.view'),
('inventory.adjust'),
('reports.view'),
('branches.view'),
('users.view');

INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions;

INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, id FROM permissions;

INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, id FROM permissions WHERE key_name IN ('pos.view','pos.create');

INSERT INTO role_permissions (role_id, permission_id)
SELECT 4, id FROM permissions WHERE key_name IN ('pos.view','pos.create');

INSERT INTO role_permissions (role_id, permission_id)
SELECT 5, id FROM permissions WHERE key_name IN ('kitchen.view','kitchen.update');

INSERT INTO role_permissions (role_id, permission_id)
SELECT 6, id FROM permissions WHERE key_name IN ('pos.view');

INSERT INTO role_permissions (role_id, permission_id)
SELECT 7, id FROM permissions WHERE key_name IN ('reports.view','inventory.view','branches.view','users.view');

INSERT INTO branches (name, address, city) VALUES
('Principal', 'Calle 123 #45-67', 'Bogotá'),
('Norte', 'Avenida 10 #22-33', 'Bogotá');

INSERT INTO users (branch_id, role_id, name, email, password_hash, created_at) VALUES
(1, 1, 'Super Admin', 'admin@pos.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9eKuX3sI5Yucs5cjox96.2', NOW());

INSERT INTO categories (name) VALUES
('Hamburguesas'),
('Bebidas'),
('Combos');

INSERT INTO products (category_id, name, price, active) VALUES
(1, 'Hamburguesa Clásica', 15000, 1),
(1, 'Hamburguesa Doble', 22000, 1),
(2, 'Gaseosa', 5000, 1),
(3, 'Combo Clásico', 20000, 1);

INSERT INTO ingredients (name, unit) VALUES
('Pan', 'unidad'),
('Carne', 'unidad'),
('Queso', 'unidad'),
('Papas', 'gramo'),
('Gaseosa', 'unidad');

INSERT INTO inventory (ingredient_id, quantity, min_stock) VALUES
(1, 200, 30),
(2, 200, 30),
(3, 200, 30),
(4, 10000, 1500),
(5, 300, 50);

INSERT INTO recipe_items (product_id, ingredient_id, quantity) VALUES
(1, 1, 1),
(1, 2, 1),
(1, 3, 1),
(2, 1, 1),
(2, 2, 2),
(2, 3, 1),
(3, 5, 1),
(4, 1, 1),
(4, 2, 1),
(4, 4, 200),
(4, 5, 1);
