-- Create database if not exists
CREATE DATABASE IF NOT EXISTS agrotrack;
USE agrotrack;

-- Create crops table
CREATE TABLE IF NOT EXISTS crops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    variety VARCHAR(100) NOT NULL,
    planting_date DATE NOT NULL,
    expected_harvest_date DATE NOT NULL,
    status ENUM('planted', 'growing', 'ready', 'harvested') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create sensors table
CREATE TABLE IF NOT EXISTS sensors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('temperature', 'humidity', 'soil_moisture', 'light') NOT NULL,
    location VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'maintenance') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create readings table
CREATE TABLE IF NOT EXISTS readings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sensor_id INT NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sensor_id) REFERENCES sensors(id)
);

-- Create actuators table
CREATE TABLE IF NOT EXISTS actuators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('irrigation', 'ventilation', 'lighting', 'heating') NOT NULL,
    status ENUM('on', 'off', 'maintenance') NOT NULL,
    location VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample crops
INSERT INTO crops (name, variety, planting_date, expected_harvest_date, status) VALUES
('Tomatoes', 'Beefsteak', '2024-03-01', '2024-06-15', 'growing'),
('Lettuce', 'Romaine', '2024-03-15', '2024-05-01', 'growing'),
('Bell Peppers', 'California Wonder', '2024-02-15', '2024-06-01', 'growing'),
('Cucumbers', 'English', '2024-03-10', '2024-06-20', 'planted'),
('Basil', 'Sweet', '2024-03-20', '2024-05-15', 'planted');

-- Insert sample sensors
INSERT INTO sensors (name, type, location, status) VALUES
('Temp Sensor 1', 'temperature', 'Greenhouse A - North', 'active'),
('Humidity Sensor 1', 'humidity', 'Greenhouse A - North', 'active'),
('Soil Moisture 1', 'soil_moisture', 'Greenhouse A - Tomatoes', 'active'),
('Light Sensor 1', 'light', 'Greenhouse A - Roof', 'active'),
('Temp Sensor 2', 'temperature', 'Greenhouse B - South', 'active'),
('Humidity Sensor 2', 'humidity', 'Greenhouse B - South', 'active');

-- Insert sample readings
INSERT INTO readings (sensor_id, value, unit, timestamp) VALUES
(1, 25.5, 'C', NOW()),
(2, 65.0, '%', NOW()),
(3, 45.0, '%', NOW()),
(4, 850.0, 'lux', NOW()),
(5, 24.8, 'C', NOW()),
(6, 68.0, '%', NOW());

-- Insert sample actuators
INSERT INTO actuators (name, type, status, location) VALUES
('Main Irrigation', 'irrigation', 'off', 'Greenhouse A'),
('Ventilation Fan 1', 'ventilation', 'off', 'Greenhouse A - North'),
('Grow Lights 1', 'lighting', 'off', 'Greenhouse A'),
('Heating System', 'heating', 'off', 'Greenhouse A'),
('Drip Irrigation', 'irrigation', 'off', 'Greenhouse B'),
('Ventilation Fan 2', 'ventilation', 'off', 'Greenhouse B - South'); 