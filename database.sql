-- ResQ Emergency Response System - Database Schema
-- For Laragon MySQL

CREATE DATABASE IF NOT EXISTS resq CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE resq;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    user_type ENUM('user', 'responder', 'dispatcher', 'admin') DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    email_verified_at DATETIME,
    phone_verified_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User Profiles Table
CREATE TABLE IF NOT EXISTS user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    date_of_birth DATE,
    address VARCHAR(500),
    city VARCHAR(100),
    state VARCHAR(100),
    zip_code VARCHAR(20),
    profile_picture VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Emergency Contacts Table
CREATE TABLE IF NOT EXISTS emergency_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    relationship VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Medical Info Table
CREATE TABLE IF NOT EXISTS medical_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    blood_type VARCHAR(10),
    allergies VARCHAR(1000),
    medical_conditions VARCHAR(1000),
    medications VARCHAR(1000),
    medical_notes VARCHAR(2000),
    organ_donor BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Emergency Types Table
CREATE TABLE IF NOT EXISTS emergency_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    icon VARCHAR(50),
    color VARCHAR(20),
    description VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Emergency Agencies Table
CREATE TABLE IF NOT EXISTS emergency_agencies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    type VARCHAR(50),
    phone VARCHAR(20),
    email VARCHAR(255),
    address VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Emergency Requests Table
CREATE TABLE IF NOT EXISTS emergency_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requester_id INT NOT NULL,
    emergency_type_id INT NOT NULL,
    incident_number VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    address VARCHAR(500),
    status ENUM('pending', 'accepted', 'responding', 'arrived', 'completed', 'cancelled', 'rejected', 'pending_verification') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    is_sos BOOLEAN DEFAULT FALSE,
    is_verified BOOLEAN DEFAULT TRUE,
    verified_at DATETIME,
    completed_at DATETIME,
    last_location_update DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (requester_id) REFERENCES users(id),
    FOREIGN KEY (emergency_type_id) REFERENCES emergency_types(id)
);

-- Responders Table
CREATE TABLE IF NOT EXISTS responders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    agency_id INT,
    badge_number VARCHAR(50),
    vehicle_info VARCHAR(255),
    status ENUM('available', 'busy', 'offline', 'on_duty') DEFAULT 'offline',
    current_latitude DECIMAL(10, 8),
    current_longitude DECIMAL(11, 8),
    last_location_update DATETIME,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (agency_id) REFERENCES emergency_agencies(id)
);

-- Incident Responders (Pivot Table)
CREATE TABLE IF NOT EXISTS incident_responders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emergency_request_id INT NOT NULL,
    responder_id INT NOT NULL,
    status ENUM('assigned', 'accepted', 'rejected', 'en_route', 'arrived', 'completed') DEFAULT 'assigned',
    assigned_at DATETIME,
    accepted_at DATETIME,
    rejected_at DATETIME,
    rejection_reason VARCHAR(500),
    arrived_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (emergency_request_id) REFERENCES emergency_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (responder_id) REFERENCES responders(id)
);

-- Messages Table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emergency_request_id INT NOT NULL,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    read_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emergency_request_id) REFERENCES emergency_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

-- Media Table
CREATE TABLE IF NOT EXISTS media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emergency_request_id INT NOT NULL,
    user_id INT NOT NULL,
    type ENUM('photo', 'video', 'audio', 'document') NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT,
    mime_type VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emergency_request_id) REFERENCES emergency_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Facilities Table
CREATE TABLE IF NOT EXISTS facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('hospital', 'police_station', 'fire_station', 'evacuation_center', 'rescue') NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    address VARCHAR(500),
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Incident History Table
CREATE TABLE IF NOT EXISTS incident_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emergency_request_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    notes TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emergency_request_id) REFERENCES emergency_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Feedback Table
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emergency_request_id INT NOT NULL,
    user_id INT NOT NULL,
    responder_id INT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emergency_request_id) REFERENCES emergency_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (responder_id) REFERENCES responders(id)
);

-- Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    data JSON,
    is_read BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Announcements Table
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('announcement', 'alert', 'weather', 'disaster') DEFAULT 'announcement',
    is_public BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    expires_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Audit Logs Table
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    old_values JSON,
    new_values JSON,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert Default Data
INSERT INTO emergency_types (name, code, icon, color, description) VALUES
('Medical Emergency', 'medical', '🚑', '#e74c3c', 'Medical emergencies including heart attacks, injuries, etc.'),
('Fire', 'fire', '🚒', '#e67e22', 'Fire emergencies, building fires, wildfires'),
('Crime / Police', 'crime', '🚓', '#3498db', 'Criminal activities, theft, assault'),
('Vehicular Accident', 'accident', '🚗', '#9b59b6', 'Car accidents, traffic collisions'),
('Flood', 'flood', '🌊', '#1abc9c', 'Flooding, water emergencies'),
('Earthquake', 'earthquake', '🌎', '#f39c12', 'Earthquake emergencies'),
('Landslide', 'landslide', '🌋', '#8e44ad', 'Landslide, mudslide'),
('Typhoon / Storm', 'storm', '🌪️', '#34495e', 'Typhoons, severe storms'),
('Electrical Hazard', 'electrical', '⚡', '#f1c40f', 'Electrical hazards, power lines down'),
('Missing Person', 'missing', '🧒', '#e91e63', 'Missing persons, lost children'),
('Building Collapse', 'collapse', '🏠', '#795548', 'Building collapse, structural failure'),
('Hazardous Materials', 'hazmat', '☣️', '#607d8b', 'Chemical spills, hazardous materials'),
('Other Emergency', 'other', '❓', '#95a5a6', 'Other types of emergencies');

INSERT INTO emergency_agencies (name, code, type, phone) VALUES
('National Police', 'police', 'police', '911'),
('Fire Department', 'fire', 'fire', '912'),
('Emergency Medical Services', 'ems', 'medical', '913'),
('Coast Guard', 'coastguard', 'rescue', '914'),
('Disaster Management Office', 'dmo', 'disaster', '915'),
('Municipal Health Office', 'health', 'medical', '916');

INSERT INTO facilities (name, type, latitude, longitude, address, phone) VALUES
('Central Hospital', 'hospital', 14.5995, 120.9842, 'Manila City', '911'),
('Fire Station No. 1', 'fire_station', 14.5895, 120.9822, 'Manila City', '912'),
('Police Station', 'police_station', 14.6095, 120.9862, 'Manila City', '911'),
('Evacuation Center A', 'evacuation_center', 14.5795, 120.9802, 'Manila City', '');

-- Insert Admin User (password: password)
INSERT INTO users (name, email, password, phone, user_type, is_active, email_verified_at) VALUES
('System Administrator', 'admin@resq.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+639123456789', 'admin', TRUE, NOW());