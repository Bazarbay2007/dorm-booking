CREATE DATABASE IF NOT EXISTS dorm_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dorm_booking;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  room_number VARCHAR(10) NOT NULL UNIQUE,
  floor TINYINT NOT NULL,
  capacity TINYINT NOT NULL,
  price INT NOT NULL,
  description VARCHAR(255) DEFAULT ''
);

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  room_id INT NOT NULL,
  check_in DATE NOT NULL,
  check_out DATE NOT NULL,
  note VARCHAR(255) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

INSERT INTO rooms (room_number, floor, capacity, price, description) VALUES
('101',1,2,15000,'Екі кісілік, жеке жуынатын бөлме'),
('102',1,3,12000,'Үш кісілік, терезесі аулаға қарайды'),
('201',2,2,16000,'Жаңа жөндеу, Wi-Fi'),
('202',2,4,10000,'Төрт кісілік, кең бөлме'),
('301',3,1,25000,'Бір кісілік, жеке бөлме'),
('302',3,2,17000,'Екі кісілік, балкон бар'),
('303',3,3,13000,'Үш кісілік'),
('401',4,2,18000,'Екі кісілік, оқуға арналған үстел');
