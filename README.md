# PHP Authentication & Profile Management System

## 📌 Project Overview

This project is a **PHP-based Authentication and Profile Management System** built to demonstrate real-world backend development concepts.  
It implements secure authentication, session management, caching, and multi-database integration using **MySQL, MongoDB, and Redis**.

The application allows users to register, log in, maintain authenticated sessions, view and update profile information, and log out securely.  
It is fully deployed and tested using modern cloud tools.

---

## 🚀 Live Links

### Frontend  
https://auth-system-frontend-1.netlify.app/login.html  

### Backend API (Railway)  
https://auth-system-backend-production-4178.up.railway.app  

> Note: The backend is an API service. Opening the root URL may show “Not Found”, which is expected.

---

## 📂 GitHub Repositories

- **Backend:**  
  https://github.com/KARTHIKEYAN0807/auth-system-backend  

- **Frontend:**  
  https://github.com/KARTHIKEYAN0807/auth-system-frontend  

---

## 🛠️ Technology Stack

### Backend
- PHP 8.2  
- MySQL (Authentication data)  
- MongoDB Atlas (Profile data)  
- Redis (Session management & caching)  
- Docker (Custom PHP runtime with SSL support)

### Frontend
- HTML  
- CSS / Bootstrap  
- JavaScript  
- jQuery  
- AJAX

### Deployment
- Railway (Backend + databases)  
- MongoDB Atlas (Cloud MongoDB)  
- Netlify (Frontend)

---

## 🏗️ System Architecture

This project follows **separation of concerns**, where each service is used for its strength:

| Component | Responsibility |
|--------|---------------|
| MySQL | User authentication & credentials |
| MongoDB | User profile / catalog data |
| Redis | Sessions & caching |
| PHP API | Business logic |
| Frontend | User interface |

All databases are accessed **only via the backend API**, ensuring security.

---

## 🗄️ Database Design

### MySQL – Authentication

Used for structured relational data such as login credentials.

**Users Table**
- id
- email
- password (hashed)
- created_at

**Security**
- MySQL prepared statements are used everywhere
- Prevents SQL injection
- Passwords are securely hashed

---

### MongoDB – Profile Management

Stores flexible user profile data:
- Name
- Phone
- Age
- City
- Bio

Each profile is linked using `user_id`.

**Why MongoDB?**
- Flexible schema
- Easy updates
- Suitable for evolving profile data

Profiles are handled using **upsert operations**.

---

### Redis – Session Management & Caching

Redis stores active user sessions in memory.

**Session format**


**Features**
- TTL-based automatic expiration
- Fast session validation
- Stateless backend design
- High scalability

---

## 🔐 Authentication Flow

### 1. Registration
1. User submits email and password
2. Input validation is performed
3. Password is hashed
4. User data is stored in MySQL

---

### 2. Login
1. User submits credentials
2. Backend verifies credentials via MySQL
3. On success:
   - A unique session ID is generated
   - Stored in Redis with TTL
4. Session ID is returned to the client

---

### 3. Session Validation
- Client sends `Session-Id` in request headers
- Backend validates session from Redis
- Access granted or denied accordingly

---

### 4. Logout
- Session key is removed from Redis
- User is logged out immediately

---

## ⚡ Session Management (Redis)

This project **does not use PHP default sessions**.

Instead:
- Sessions are stored in Redis
- Backend remains stateless
- Easily scalable across multiple servers

This is a **production-ready session strategy**.

---

## 🌐 AJAX & jQuery

The frontend communicates with the backend using **AJAX**.

### Usage
- Fetch profile details
- Update profile data
- Handle authenticated requests

**Benefits**
- No page reloads
- Faster UI updates
- Better user experience

jQuery is used for:
- DOM manipulation
- Event handling
- AJAX requests

---

## 🚀 Caching Strategy

Redis acts as a caching layer for session validation:
- Reduces database queries
- Improves performance
- Handles high concurrent requests efficiently

---

## 🔒 Security Measures

- Password hashing
- MySQL prepared statements
- Header-based session authentication
- Redis TTL-based session expiry
- MongoDB Atlas TLS encryption
- Environment variables for credentials
- No sensitive data exposed on frontend
- Docker-based secure PHP runtime

---

## 🐳 Docker & MongoDB Atlas

MongoDB Atlas enforces **TLS/SSL encryption**.

To ensure compatibility:
- Docker is used to install OpenSSL
- MongoDB PHP driver is compiled with SSL support
- Secure communication with Atlas is guaranteed

This makes the backend **production-ready**.

---

## 🚀 Deployment

### Frontend
- Hosted on Netlify

### Backend
- Hosted on Railway
- Exposes REST-style API endpoints

### Databases
- MySQL, MongoDB, Redis are backend services
- Not publicly exposed

---

## 🧪 How to Test

1. Open frontend link
2. Register a new user
3. Log in
4. View profile data
5. Update profile details
6. Refresh page to confirm persistence
7. Log out
8. Verify session expiration

---

## 📌 Conclusion

This project demonstrates a complete backend system using PHP with modern best practices.  
It integrates multiple databases, secure authentication, caching, session management, and cloud deployment.  
The architecture reflects real-world backend development and scalability considerations.

---

## 👤 Author

**Karthikeyan S**

