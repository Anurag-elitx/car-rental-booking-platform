-- Obsidian Automotive | Project Submission Database
-- This file contains the complete elite seed data for your fleet.

-- 1. Schema Definition (Import directly into your selected database)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'agency') NOT NULL,
    contact_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agency_id INT NOT NULL,
    model VARCHAR(255) NOT NULL,
    vehicle_number VARCHAR(50) NOT NULL UNIQUE,
    seating_capacity INT NOT NULL,
    rent_per_day DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) DEFAULT 'Sedan',
    image_filename VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agency_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    car_id INT NOT NULL,
    start_date DATE NOT NULL,
    duration_days INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    booked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
);

-- 3. Elite Seed Data (Obsidian Automotive Fleet)
-- Passwords are hashed versions of '123' (using default BCRYPT)

INSERT INTO users (id, name, email, password, role, contact_number) VALUES
(1, 'Obsidian Elite Agency', 'agency@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agency', '+971-55-123-4567'),
(2, 'Prestige Partner', 'luxury@agency.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agency', '+971-55-987-6543'),
(3, 'Test Customer', 'client@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '+971-50-555-0100');

INSERT INTO cars (agency_id, model, vehicle_number, seating_capacity, rent_per_day, category, image_filename) VALUES
(1, 'Test Audi R8', 'DXB-800', 2, 25000.00, 'Coupe', 'car1.png'),
(1, 'Volt Hyperion Mk1', 'DXB-001', 2, 45000.00, 'Coupe', 'car2.png'),
(1, 'Rosso Corsa Spyder', 'DXB-F1', 2, 35000.00, 'Convertible', 'car3.png'),
(1, 'Bentley Flying Spur', 'SHJ-444', 4, 18000.00, 'Sedan', 'car4.png'),
(1, 'Rolls Royce Ghost', 'DXB-VIP', 4, 30000.00, 'Sedan', 'car5.png'),
(2, 'Range Rover Autobiography', 'DXB-SUV-1', 5, 12000.00, 'SUV', 'car_crossover.png'),
(2, 'Mercedes-Benz G63 AMG', 'DXB-G-WAGON', 5, 15000.00, 'SUV', 'car_pickup.png'),
(2, 'BMW M5 Competition', 'DXB-M5', 4, 9000.00, 'Sedan', 'fleet2.png');
