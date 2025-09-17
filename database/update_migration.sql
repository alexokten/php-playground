-- Migration to update existing database structure
-- This handles renaming tables and updating indexes safely

-- Drop existing indexes first
DROP INDEX IF EXISTS idx_users_city;
DROP INDEX IF EXISTS idx_users_active;
DROP INDEX IF EXISTS idx_attendees_city;
DROP INDEX IF EXISTS idx_attendees_active;
DROP INDEX IF EXISTS idx_promoters_email;
DROP INDEX IF EXISTS idx_promoters_active;
DROP INDEX IF EXISTS idx_events_promoter;
DROP TABLE IF EXISTS users;

-- Create promoters table
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

-- Add promoterId to events table
ALTER TABLE events ADD COLUMN IF NOT EXISTS promoterId INT;
ALTER TABLE events ADD FOREIGN KEY IF NOT EXISTS (promoterId) REFERENCES promoters(id) ON DELETE CASCADE;

-- Update event_attendees table structure
ALTER TABLE event_attendees CHANGE userId attendeeId INT NOT NULL;
ALTER TABLE event_attendees DROP FOREIGN KEY event_attendees_ibfk_1;
ALTER TABLE event_attendees ADD FOREIGN KEY (attendeeId) REFERENCES attendees(id) ON DELETE CASCADE;

-- Add new indexes
CREATE INDEX idx_attendees_city ON attendees(city);
CREATE INDEX idx_attendees_active ON attendees(isActive);
CREATE INDEX idx_promoters_email ON promoters(email);
CREATE INDEX idx_promoters_active ON promoters(isActive);
CREATE INDEX idx_events_promoter ON events(promoterId);
