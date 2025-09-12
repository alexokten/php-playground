-- Database Migrations
-- Based on Event and User models in models/ folder

-- Create Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(255) NOT NULL,
    lastName VARCHAR(255) NOT NULL,
    dateOfBirth DATE,
    city VARCHAR(255),
    isActive BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Events Table  
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    eventDate DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    maxTickets INT DEFAULT 50,
    isActive BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create event_attendees pivot table for many-to-many relationship
CREATE TABLE IF NOT EXISTS event_attendees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (user_id, event_id)
);

-- Add indexes for better performance
CREATE INDEX idx_users_city ON users(city);
CREATE INDEX idx_users_active ON users(isActive);
CREATE INDEX idx_events_date ON events(eventDate);
CREATE INDEX idx_events_location ON events(location);
CREATE INDEX idx_events_active ON events(isActive);