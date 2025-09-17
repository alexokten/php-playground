-- Database Migrations
-- Based on Attendee, Event, and Promoter models

-- Create Attendees Table (renamed from users)
CREATE TABLE IF NOT EXISTS attendees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(255) NOT NULL,
    lastName VARCHAR(255) NOT NULL,
    dateOfBirth DATE,
    city VARCHAR(255),
    isActive BOOLEAN DEFAULT TRUE,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Promoters Table
CREATE TABLE IF NOT EXISTS promoters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    company VARCHAR(255),
    phone VARCHAR(20),
    isActive BOOLEAN DEFAULT TRUE,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Events Table (with promoter relationship)
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    eventDate DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    maxTickets INT DEFAULT 50,
    promoterId INT NOT NULL,
    isActive BOOLEAN DEFAULT TRUE,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (promoterId) REFERENCES promoters(id) ON DELETE CASCADE
);

-- Create event_attendees pivot table for many-to-many relationship
CREATE TABLE IF NOT EXISTS event_attendees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attendeeId INT NOT NULL,
    eventId INT NOT NULL,
    registeredAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attendeeId) REFERENCES attendees(id) ON DELETE CASCADE,
    FOREIGN KEY (eventId) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY uniqueAttendance (attendeeId, eventId)
);

-- Add indexes for better performance
CREATE INDEX idx_attendees_city ON attendees(city);
CREATE INDEX idx_attendees_active ON attendees(isActive);
CREATE INDEX idx_promoters_email ON promoters(email);
CREATE INDEX idx_promoters_active ON promoters(isActive);
CREATE INDEX idx_events_date ON events(eventDate);
CREATE INDEX idx_events_location ON events(location);
CREATE INDEX idx_events_active ON events(isActive);
CREATE INDEX idx_events_promoter ON events(promoterId);
