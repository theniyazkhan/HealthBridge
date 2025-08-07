CREATE TABLE `medicines` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) DEFAULT NULL,
  `price` DECIMAL(10,2) DEFAULT NULL,
  `stock` INT(11) DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data into medicines
INSERT INTO `medicines` (`id`, `name`, `price`, `stock`, `image`) VALUES
(4, 'Ace 500mg', 1.20, 104, 'ace-500-mg-tablet.jpg'),
(5, 'Maxpro 20mg', 4.00, 48, 'maxpro-20-mg-tablet.jpg'),
(6, 'Losectil 20mg', 5.50, 18, 'losectil-20-mg-capsule.webp'),
(7, 'Nexum 40mg', 6.00, 22, 'nexum-mups-40mg.jpg'),
(8, 'Monas 10mg', 2.50, 82, 'monas-10-mg-tablet.webp'),
(10, 'Bisocor 2.5mg', 4.20, 74, 'bisocor-25-mg-tablet.webp'),
(11, 'Histacin', 1.00, 199, 'histacin-4-mg-tablet.webp'),
(12, 'Pantonix 20mg', 7.50, 96, 'pantonix-20-mg-tablet.webp'),
(13, 'Amdocal 5mg', 3.75, 47, 'amdocal-5-mg-tablet.jpg'),
(14, 'Napa Extra 500mg', 1.50, 99, 'napa-extra-500-mg-tablet.webp'),
(16, 'Bislol', 40.00, 37, 'bislol-25-mg-tablet.jpg'),
(17, 'Entacyd Plus', 25.50, 45, 'entacyd-plus-400-mg-tablet.webp');

-- Create users table (needed for foreign key reference)
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dummy user for testing orders
INSERT INTO `users` (`id`, `username`) VALUES (1, 'test_user');

-- Create orders table
CREATE TABLE `orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `medicine_id` INT(11) DEFAULT NULL,
  `quantity` INT(11) DEFAULT NULL,
  `order_date` DATE DEFAULT NULL,
  `payment_method` VARCHAR(50) DEFAULT NULL,
  `bkash_number` VARCHAR(20) DEFAULT NULL,
  `bkash_txn` VARCHAR(30) DEFAULT NULL,
  `card_number` VARCHAR(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `medicine_id` (`medicine_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;