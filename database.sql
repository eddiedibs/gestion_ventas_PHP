-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS gestion_ventas;
USE gestion_ventas;

-- Crear la tabla de clientes
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    cedula_rif VARCHAR(20) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    direccion TEXT
);

-- Crear la tabla de productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    categoria VARCHAR(100),
    precio_base DECIMAL(10, 2) NOT NULL,
    cantidad_total Integer(10) NOT NULL,
    descuento DECIMAL(10, 2) DEFAULT 0.00
);

-- Insertar productos de prueba
INSERT INTO productos (nombre, categoria, precio_base, cantidad_total, descuento) VALUES
('Desengrasante bicarbonato naranja limon ajax 1L', 'Limpieza de cocina', 5.42, 50, 0.05),
('Detergente fragancia floral multi clean 400gr 1x30 (l391) polar', 'Lavandería', 1.50, 30, 0.10),
('Lavaplatos en crema limon axion 450 gr', 'Limpieza de cocina', 4.53, 25, 0.00),
('Esponja multiuso izy clean', 'Limpieza de cocina', 1.39, 35, 0.20),
('Desifectante fresh ivon 1000ml', 'Limpieza general', 3.73, 20, 0.15),
('Lavaplatos liquido salt citrus ajax ultra 366ml (impor)', 'Limpieza de cocina', 3.15, 45, 0.08),
('Esponja doble uso slim limpia sol (3 pack) ', 'Limpieza de cocina', 2.20, 26, 0.00),
('Destapador de cañeria diablo rojo pino 1l', 'Limpieza de baño', 8.84, 40, 0.10),
('Jabon en polvo fragancia citrica multi clean 5 kg (l384)', 'Lavandería', 12.53, 35, 0.05),
('Lavaplatos en crema multiuso axion 450 gr', 'Limpieza de cocina', 4.83, 30, 0.00);

-- Crear la tabla de carrito
CREATE TABLE IF NOT EXISTS carritos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

-- Crear la tabla de items del carrito
CREATE TABLE IF NOT EXISTS items_carrito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    carrito_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (carrito_id) REFERENCES carritos(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Crear la tabla de pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

-- Crear la tabla de items de pedidos
CREATE TABLE IF NOT EXISTS items_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);



-- Crear la tabla de vendedores
CREATE TABLE vendedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE
);

-- Crear la tabla de ventas
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    vendedor_id INT,
    fecha DATE NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    impuesto DECIMAL(10, 2) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (vendedor_id) REFERENCES vendedores(id)
);

-- Crear la tabla de detalles de ventas
CREATE TABLE detalles_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT,
    producto_id INT,
    cantidad INT NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

