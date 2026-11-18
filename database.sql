CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 role ENUM('admin','customer') NOT NULL DEFAULT 'customer',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE products (
 id INT AUTO_INCREMENT PRIMARY KEY,
 category_id INT NULL,
 name VARCHAR(150) NOT NULL,
 description TEXT,
 price DECIMAL(12,2) NOT NULL DEFAULT 0,
 stock INT NOT NULL DEFAULT 0,
 image VARCHAR(500),
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_products_category FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE carts (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_cart_user(user_id),
 CONSTRAINT fk_carts_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE cart_items (
 id INT AUTO_INCREMENT PRIMARY KEY,
 cart_id INT NOT NULL,
 product_id INT NOT NULL,
 qty INT NOT NULL DEFAULT 1,
 UNIQUE KEY uq_cart_product(cart_id,product_id),
 CONSTRAINT fk_cartitems_cart FOREIGN KEY(cart_id) REFERENCES carts(id) ON DELETE CASCADE,
 CONSTRAINT fk_cartitems_product FOREIGN KEY(product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE orders (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 recipient VARCHAR(100) NOT NULL,
 phone VARCHAR(30) NOT NULL,
 address TEXT NOT NULL,
 notes TEXT,
 total DECIMAL(12,2) NOT NULL,
 status ENUM('Menunggu','Diproses','Dikirim','Selesai','Dibatalkan') DEFAULT 'Menunggu',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_orders_user FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE order_items (
 id INT AUTO_INCREMENT PRIMARY KEY,
 order_id INT NOT NULL,
 product_id INT NULL,
 product_name VARCHAR(150) NOT NULL,
 price DECIMAL(12,2) NOT NULL,
 qty INT NOT NULL,
 subtotal DECIMAL(12,2) NOT NULL,
 CONSTRAINT fk_orderitems_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
 CONSTRAINT fk_orderitems_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO categories(name) VALUES ('Makanan Tradisional'),('Kerajinan'),('Hasil Pertanian'),('Minuman');

-- Password admin awal: admin123 (wajib segera diganti setelah login).
-- Hash di bawah menggunakan password_hash() PHP untuk password admin123.
INSERT INTO users(name,email,password,role) VALUES
('Administrator','admin@pasarkita.test','$2y$12$CYafTJ9I/gjxbdK.QjNaYep.nvkSaFKkFrw8Wjx35fNqVSYfvqr82','admin');

INSERT INTO products(category_id,name,description,price,stock,image) VALUES
(1,'Keripik Pisang','Keripik pisang renyah produksi rumahan.',18000,30,'https://images.unsplash.com/photo-1587132137056-bfbf0166836e?w=800'),
(1,'Dodol Tradisional','Dodol manis dengan resep tradisional.',25000,20,'https://images.unsplash.com/photo-1605197161470-5d6d6f2b5a4f?w=800'),
(2,'Anyaman Bambu','Kerajinan anyaman bambu buatan pengrajin lokal.',45000,12,'https://images.unsplash.com/photo-1590736969955-71cc94901144?w=800'),
(3,'Kopi Bubuk Lokal','Kopi bubuk pilihan dari petani lokal.',32000,25,'https://images.unsplash.com/photo-1442512595331-e89e73853f31?w=800');
