# Leads Management API (Laravel)

A robust RESTful API for managing leads, built with Laravel. This project supports JWT authentication, CRUD operations for leads, date filtering, and a comprehensive test suite.

---

## Table of Contents
- [Features](#features)
- [Project Structure](#project-structure)
- [Endpoints](#endpoints)
- [Lead Model](#lead-model)
- [Authentication](#authentication)
- [Date Filtering](#date-filtering)
- [Testing](#testing)
- [Setup & Running](#setup--running)
- [Environment Variables](#environment-variables)
- [Troubleshooting](#troubleshooting)

---

## Features
- JWT-based authentication
- CRUD for leads (Create, Read, Update, Delete)
- Date filtering for GET /api/leads
- Validation for all endpoints
- Factory-based test data
- SQLite in-memory testing
- 40+ automated tests

---

## Project Structure
```
app/
	Http/
		Controllers/
			LeadController.php   # Main API logic for leads
			AuthController.php   # JWT login & profile
	Models/
		Lead.php              # Lead Eloquent model
		User.php              # User Eloquent model
config/
	auth.php                # Auth guards & providers
	jwt.php                 # JWT config
routes/
	api.php                 # API route definitions
resources/
	views/                  # Blade templates (not used for API)
database/
	migrations/             # Table definitions
	factories/              # Model factories for tests
	seeders/                # DatabaseSeeder for test users
vendor/                   # Composer dependencies
tests/
	Feature/                # API & integration tests
	Unit/                   # Model & logic tests
```

---

## Endpoints

### Authentication
- `POST /api/auth/login` — Login, returns JWT token
- `GET /api/auth/me` — Get current user profile (JWT required)

### Leads
- `POST /api/leads` — Create a new lead
- `GET /api/leads` — Get all leads (supports date filtering)
- `GET /api/leads/{id}` — Get lead by ID
- `PUT /api/leads/{id}` — Update a lead
- `DELETE /api/leads/{id}` — Delete a lead

#### Example: Create Lead
```bash
curl -X POST http://localhost:8000/api/leads \
	-H "Content-Type: application/json" \
	-H "Authorization: Bearer YOUR_JWT_TOKEN" \
	-d '{
		"name": "John Doe",
		"email": "john.doe@example.com",
		"leadSource": "Website",
		"leadStatus": "NEW"
	}'
```

#### Example: Get All Leads (with date filtering)
```bash
curl -X GET "http://localhost:8000/api/leads?from=2025-10-01&to=2025-10-31" \
	-H "Authorization: Bearer YOUR_JWT_TOKEN"
```

---

## Lead Model
- **Fields:**
	- `name` (string, required)
	- `email` (string, required, unique)
	- `leadSource` (string, optional)
	- `leadStatus` (string, required)
	- `createdAt`, `updatedAt` (timestamps in ISO format)
- **Validation:** All endpoints validate input and return errors for invalid data.
- **Format:** API uses camelCase for field names (e.g., `leadSource`, `leadStatus`, `createdAt`)

#### Example Lead Response:
```json
{
    "id": 1,
    "name": "Wits University",
    "email": "wits@lead.ac.za",
    "leadSource": "Social Media",
    "leadStatus": "Qualified",
    "createdAt": "2025-10-27T00:03:10.222884",
    "updatedAt": "2025-10-27T00:09:46.998213"
}
```

---

## Authentication
- Uses [tymon/jwt-auth](https://github.com/tymondesigns/jwt-auth)
- Login returns a JWT token
- All protected endpoints require `Authorization: Bearer <token>` header
- User model implements JWTSubject

### Default Login Credentials
For testing purposes, you can use these default credentials (created by the database seeder):

```json
{
    "email": "test@example.com",
    "password": "password"
}
```

#### Example Login Request:
```bash
curl -X POST http://localhost:8000/api/auth/login \
    -H "Content-Type: application/json" \
    -d '{
        "email": "test@example.com",
        "password": "password"
    }'
```

#### Example Response:
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600
}
```

---

## Date Filtering
- `GET /api/leads?from=YYYY-MM-DD&to=YYYY-MM-DD` returns leads created in the date range
- Dates are inclusive and can be in `YYYY-MM-DD` or ISO8601 format

---

## Testing
- **Unit tests:** Validate model logic, fillable fields, JWT methods
- **Feature tests:** Cover all API endpoints, authentication, validation, CRUD, date filtering
- **Factories:** Used for generating test data
- **Database:** Tests use in-memory SQLite for speed and isolation

### Run All Tests
```bash
php artisan test
```

### Run Specific Test File
```bash
php artisan test tests/Feature/LeadApiTest.php
```

### Run With Coverage (if xdebug installed)
```bash
php artisan test --coverage
```

---

## Setup & Running

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Configure Environment
- Copy `.env.example` to `.env`
- Set DB and JWT variables
- Generate app key:
	```bash
	php artisan key:generate
	```
- Generate JWT secret:
	```bash
	php artisan jwt:secret
	```

### 3. Run Migrations & Seeders
```bash
php artisan migrate --seed
```

### 4. Start Server
```bash
php artisan serve
# Or alternatively:
php -S localhost:8000 -t public
```

The API will be available at `http://localhost:8000` (or the port specified by `php artisan serve`).

### 5. Test the API
Once the server is running, you can test the authentication:

```bash
# Login to get JWT token
curl -X POST http://localhost:8000/api/auth/login \
    -H "Content-Type: application/json" \
    -d '{
        "email": "test@example.com",
        "password": "password"
    }'

# Use the returned token for protected endpoints
curl -X GET http://localhost:8000/api/leads \
    -H "Authorization: Bearer YOUR_JWT_TOKEN_HERE"
```

---

## Environment Variables
- `DB_CONNECTION`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` — Database config
- `JWT_SECRET` — JWT signing key
- `APP_KEY` — Laravel app key

---

## Troubleshooting
- **Port in use:** Change port in server command
- **JWT errors:** Ensure `JWT_SECRET` is set and valid
- **Database errors:** Confirm DB is running and credentials are correct
- **Test failures:** Run `php artisan config:clear` and `php artisan migrate:fresh --seed` before testing
