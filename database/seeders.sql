-- Dummy Data Seeding
-- Populates users and events tables with realistic test data

-- Clear existing data (optional - comment out if you want to keep existing data)
-- DELETE FROM event_attendees;
-- DELETE FROM events;
-- DELETE FROM users;

-- Insert dummy users
INSERT INTO users (firstName, lastName, dateOfBirth, city, isActive) VALUES
('John', 'Smith', '1990-05-15', 'New York', TRUE),
('Sarah', 'Johnson', '1985-08-22', 'Los Angeles', TRUE),
('Michael', 'Brown', '1992-12-03', 'Chicago', TRUE),
('Emma', 'Davis', '1988-03-17', 'Houston', TRUE),
('James', 'Wilson', '1995-07-09', 'Phoenix', FALSE),
('Olivia', 'Miller', '1987-11-30', 'Philadelphia', TRUE),
('William', 'Moore', '1993-01-25', 'San Antonio', TRUE),
('Ava', 'Taylor', '1989-09-14', 'San Diego', TRUE),
('Alexander', 'Anderson', '1986-04-06', 'Dallas', TRUE),
('Isabella', 'Thomas', '1994-10-21', 'San Jose', FALSE),
('Benjamin', 'Jackson', '1991-06-18', 'Austin', TRUE),
('Sophia', 'White', '1983-02-12', 'Jacksonville', TRUE),
('Mason', 'Harris', '1996-08-07', 'Fort Worth', TRUE),
('Charlotte', 'Martin', '1990-12-29', 'Columbus', TRUE),
('Lucas', 'Garcia', '1988-05-03', 'Charlotte', TRUE);

-- Insert dummy events
INSERT INTO events (title, description, eventDate, location, maxTickets, isActive) VALUES
('PHP Conference 2024', 'Annual PHP developer conference featuring the latest in PHP development, frameworks, and best practices.', '2024-06-15 09:00:00', 'New York', 200, TRUE),
('Web Development Workshop', 'Hands-on workshop covering modern web development techniques including React, Vue, and PHP.', '2024-07-20 10:00:00', 'Los Angeles', 50, TRUE),
('Database Design Seminar', 'Learn advanced database design patterns and optimization techniques for large-scale applications.', '2024-08-10 14:00:00', 'Chicago', 75, TRUE),
('API Development Bootcamp', 'Intensive 3-day bootcamp on building robust REST APIs with PHP and modern frameworks.', '2024-09-05 09:00:00', 'Houston', 30, TRUE),
('DevOps for Developers', 'Introduction to DevOps practices including CI/CD, containerization, and cloud deployment.', '2024-10-12 13:00:00', 'Phoenix', 100, TRUE),
('Laravel Deep Dive', 'Advanced Laravel techniques including Eloquent optimization, testing, and package development.', '2024-11-18 10:00:00', 'Philadelphia', 80, TRUE),
('JavaScript & PHP Integration', 'Best practices for integrating JavaScript frontend frameworks with PHP backends.', '2024-12-03 09:30:00', 'San Antonio', 60, TRUE),
('Code Review Best Practices', 'Learn how to conduct effective code reviews and improve team collaboration.', '2025-01-22 14:30:00', 'San Diego', 40, TRUE),
('Testing Strategies Workshop', 'Comprehensive guide to unit testing, integration testing, and TDD in PHP applications.', '2025-02-28 11:00:00', 'Dallas', 55, TRUE),
('Performance Optimization', 'Techniques for optimizing PHP application performance and handling high traffic loads.', '2025-03-15 13:00:00', 'San Jose', 70, TRUE),
('Security in Web Applications', 'Essential security practices for protecting PHP web applications from common vulnerabilities.', '2025-04-08 10:30:00', 'Austin', 90, TRUE),
('Past Event Example', 'This event already happened - for testing past event functionality.', '2023-12-15 19:00:00', 'Jacksonville', 25, FALSE),
('Microservices Architecture', 'Building scalable applications using microservices architecture with PHP.', '2025-05-20 09:00:00', 'Fort Worth', 120, TRUE),
('GraphQL with PHP', 'Introduction to GraphQL and how to implement it in PHP applications.', '2025-06-12 14:00:00', 'Columbus', 45, TRUE),
('Mobile API Development', 'Designing and building APIs specifically for mobile applications using PHP.', '2025-07-08 11:30:00', 'Charlotte', 65, TRUE);

-- Create some event attendee relationships (users attending events)
INSERT INTO event_attendees (user_id, event_id, registered_at) VALUES
(1, 1, '2024-05-01 10:30:00'),  -- John attending PHP Conference
(1, 3, '2024-07-01 14:20:00'),  -- John attending Database Seminar
(2, 1, '2024-05-02 09:15:00'),  -- Sarah attending PHP Conference
(2, 2, '2024-06-15 16:45:00'),  -- Sarah attending Web Dev Workshop
(3, 4, '2024-08-01 11:00:00'),  -- Michael attending API Bootcamp
(4, 1, '2024-05-05 13:30:00'),  -- Emma attending PHP Conference
(4, 5, '2024-09-10 10:15:00'),  -- Emma attending DevOps event
(6, 6, '2024-10-01 12:00:00'),  -- Olivia attending Laravel Deep Dive
(7, 2, '2024-06-20 15:30:00'),  -- William attending Web Dev Workshop
(8, 3, '2024-07-05 09:45:00'),  -- Ava attending Database Seminar
(9, 7, '2024-11-01 14:00:00'),  -- Alexander attending JS & PHP Integration
(11, 1, '2024-05-08 11:20:00'), -- Benjamin attending PHP Conference
(12, 8, '2024-12-15 10:30:00'), -- Sophia attending Code Review workshop
(13, 9, '2025-01-10 16:15:00'), -- Mason attending Testing workshop
(14, 10, '2025-02-01 13:45:00'), -- Charlotte attending Performance workshop
(15, 11, '2025-03-01 09:30:00'); -- Lucas attending Security workshop