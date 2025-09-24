# PHP Playground - Architecture Guide

## Simple Layer Explanation

### **Model** - "What am I?"
- **Purpose**: Defines what the data IS and what it can do
- **Contains**: Properties, validation rules, relationships, domain logic
- **Example**: `$event->isUpcoming()` - the Event knows if it's upcoming

### **Repository** - "Where do I find data?"  
- **Purpose**: Handles getting and saving data to/from database
- **Contains**: Query methods, CRUD operations, data access only
- **Example**: `$eventRepo->findAll()` - gets all events from database

### **Service** - "How do I do complex business stuff?"
- **Purpose**: Contains business workflows and complex operations
- **Contains**: Business rules, multi-step processes, coordination between repositories
- **Example**: `$eventService->bookEventForUser()` - handles the entire booking process

### **Controller** - "How do I handle web requests?"
- **Purpose**: Handles HTTP requests and responses
- **Contains**: Input validation, calling services, formatting responses
- **Example**: Receives "POST /events", validates data, calls service, returns JSON

## Key Principles:
  - models/ - Data representation only
  - repositories/ - Database access only
  - services/ - Business logic only
  - controllers/ - HTTP request/response only
  - public/ - Web-accessible files only

## Flow Example: Booking an Event
```
1. User clicks "Book Event" button
2. Controller receives HTTP request
3. Controller validates the input data
4. Controller calls EventService->bookEvent()
5. Service checks business rules (is event full?)
6. Service calls EventRepository->find() to get event
7. Repository queries database via Model
8. Model returns Event object with methods like isUpcoming()
9. Service completes booking workflow
10. Controller returns success/error response
```

### Model
What is a thing, how do you define it. For example, the concept of an event.

This is about the CONCEPT of an event
```
public function isUpcoming(): bool    // Domain rule: what makes an event "upcoming"
public function isPast(): bool        // Domain rule: what makes an event "past"
public function canBeBooked(): bool   // Domain rule: booking eligibility
```

This works regardless of WHERE the data is stored

# Phinx

## Migrations

Run single commands without logging in
```
  docker-compose exec server php vendor/bin/phinx status
  docker-compose exec server php vendor/bin/phinx migrate
```
