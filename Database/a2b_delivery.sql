-- ===============================================================
-- DATABASE: a2b_delivery
-- SYSTEM: A To B Delivery Service Website
-- VERSION: 1.0 (Leaflet Integrated)
-- CREATED BY: Sai Htet Aung Hlaing
-- ===============================================================

CREATE DATABASE IF NOT EXISTS a2b_delivery CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE a2b_delivery;

-- ===============================================================
-- USERS TABLE (Senders & Drivers)
-- ===============================================================
CREATE TABLE users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('sender', 'driver') NOT NULL DEFAULT 'sender',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='User accounts (Senders & Drivers)';

-- ===============================================================
-- DRIVER PROFILES
-- ===============================================================
CREATE TABLE driver_profiles (
    driver_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    vehicle_type ENUM('Bike', 'Motorbike', 'Car') NOT NULL,
    vehicle_number VARCHAR(50) NOT NULL,
    license_number VARCHAR(100) NOT NULL,
    experience_years ENUM('None', 'Less than 1 year', 'Above 1 year') DEFAULT 'None',
    profile_photo VARCHAR(255),
    license_photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Driver-specific documents and information';

-- ===============================================================
-- ADMINS TABLE
-- ===============================================================
CREATE TABLE admins (
    admin_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'super_admin') NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (role),
    INDEX (email)
) ENGINE=InnoDB COMMENT='Administrative users for managing the system';

-- ===============================================================
-- DELIVERY REQUESTS TABLE
-- ===============================================================
CREATE TABLE delivery_requests (
    request_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id INT UNSIGNED NOT NULL,
    driver_id INT UNSIGNED DEFAULT NULL,
    pickup_location VARCHAR(255) NOT NULL,
    drop_location VARCHAR(255) NOT NULL,
    item_description VARCHAR(255) NOT NULL,
    weight VARCHAR(50),
    is_fragile ENUM('Yes', 'No') DEFAULT 'No',
    estimated_price DECIMAL(10,2) DEFAULT 0.00,
    delivery_status ENUM('pending','accepted','picked_up','in_transit','delivered','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Delivery requests from senders to drivers';

ALTER TABLE `delivery_requests` ADD `preferred_pickup_datetime` DATETIME NULL DEFAULT NULL AFTER `estimated_price`;
ALTER TABLE `delivery_requests` ADD `payment_method` ENUM('Cash','Card','Wallet') NOT NULL DEFAULT 'Cash' AFTER `preferred_pickup_datetime`;

-- ===============================================================
-- DELIVERY TRACKING TABLE
-- ===============================================================
CREATE TABLE delivery_tracking (
    track_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id INT UNSIGNED NOT NULL,
    driver_id INT UNSIGNED NOT NULL,
    current_status ENUM('accepted','picked_up','in_transit','delivered','cancelled') NOT NULL,
    current_lat VARCHAR(100),
    current_long VARCHAR(100),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES delivery_requests(request_id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Real-time tracking information for active deliveries';

-- ===============================================================
-- PAYMENTS TABLE
-- ===============================================================
CREATE TABLE payments (
    payment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id INT UNSIGNED NOT NULL,
    sender_id INT UNSIGNED NOT NULL,
    driver_id INT UNSIGNED DEFAULT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method ENUM('Cash','Card','Wallet') DEFAULT 'Cash',
    payment_status ENUM('Pending','Completed','Failed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES delivery_requests(request_id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Payments made by senders for delivery services';

-- ===============================================================
-- FEEDBACK TABLE
-- ===============================================================
CREATE TABLE feedback (
    feedback_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id INT UNSIGNED NOT NULL,
    sender_id INT UNSIGNED NOT NULL,
    driver_id INT UNSIGNED DEFAULT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES delivery_requests(request_id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='User feedback and driver ratings after delivery';

CREATE TABLE contact_messages (
    message_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);


-- ===============================================================
-- END OF DATABASE SCHEMA
-- ===============================================================
