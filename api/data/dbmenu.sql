DROP DATABASE IF EXISTS dbmenu;
CREATE DATABASE dbmenu;
USE dbmenu;


-- =========================================================
-- 1. TABLA CLIENTES
-- =========================================================

CREATE TABLE CLIENTES (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ci VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    direccion VARCHAR(250),
    telefono VARCHAR(15) NOT NULL
) ENGINE=InnoDB;


-- =========================================================
-- 2. TABLA EMPLEADOS
-- =========================================================

CREATE TABLE EMPLEADOS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ci VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    cargo VARCHAR(50) NOT NULL
) ENGINE=InnoDB;


-- =========================================================
-- 3. TABLA PRODUCTOS
-- =========================================================

CREATE TABLE PRODUCTOS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cod_barras VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(100) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    precio_unitario DECIMAL(10,2) NOT NULL,

    CHECK (stock >= 0),
    CHECK (precio_unitario > 0)
) ENGINE=InnoDB;


-- =========================================================
-- 4. TABLA USUARIOS
-- Relación: USUARIOS -> EMPLEADOS
-- =========================================================

CREATE TABLE USUARIOS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    cod_empleado INT NOT NULL,

    CONSTRAINT fk_usuario_empleado
        FOREIGN KEY (cod_empleado)
        REFERENCES EMPLEADOS(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;


-- =========================================================
-- 5. TABLA PEDIDOS
-- Relaciones: PEDIDOS -> CLIENTES
--             PEDIDOS -> EMPLEADOS
-- =========================================================

CREATE TABLE PEDIDOS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cod_cliente INT NOT NULL,
    fecha_compra DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cantidad INT NOT NULL,
    cod_empleado INT NOT NULL,

    CHECK (cantidad > 0),

    CONSTRAINT fk_pedido_cliente
        FOREIGN KEY (cod_cliente)
        REFERENCES CLIENTES(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_pedido_empleado
        FOREIGN KEY (cod_empleado)
        REFERENCES EMPLEADOS(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;


-- =========================================================
-- 6. TABLA PEDIDO_PRODUCTOS
-- Relaciones: PEDIDO_PRODUCTOS -> PRODUCTOS
--             PEDIDO_PRODUCTOS -> PEDIDOS
-- =========================================================

CREATE TABLE PEDIDO_PRODUCTOS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cod_producto INT NOT NULL,
    cod_pedido INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    CHECK (cantidad > 0),
    CHECK (precio_unitario > 0),
    CHECK (descuento >= 0),

    CONSTRAINT fk_pedido_producto_producto
        FOREIGN KEY (cod_producto)
        REFERENCES PRODUCTOS(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_pedido_producto_pedido
        FOREIGN KEY (cod_pedido)
        REFERENCES PEDIDOS(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- =========================================================
-- 7. TABLA EMPLEADO_PEDIDOS
-- Relación muchos a muchos entre EMPLEADOS y PEDIDOS
-- =========================================================

CREATE TABLE EMPLEADO_PEDIDOS (
    cod_pedido INT NOT NULL,
    cod_empleado INT NOT NULL,
    fecha DATE NOT NULL DEFAULT (CURRENT_DATE),

    PRIMARY KEY (cod_pedido, cod_empleado),

    CONSTRAINT fk_empleado_pedido_pedido
        FOREIGN KEY (cod_pedido)
        REFERENCES PEDIDOS(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_empleado_pedido_empleado
        FOREIGN KEY (cod_empleado)
        REFERENCES EMPLEADOS(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

