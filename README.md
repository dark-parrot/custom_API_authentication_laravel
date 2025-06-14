# Custom API Authentication System

A Laravel-based custom API authentication system using custom tokens stored in the database. This system provides secure user registration, login, logout, and user management without relying on Laravel Sanctum or Passport.

## 🚀 Features

- **Custom Token Authentication** - Uses custom-generated tokens stored in database
- **Single Session Management** - Only one active session per user
- **RESTful API Endpoints** - Clean API structure for frontend integration
- **Secure Password Handling** - Bcrypt password hashing
- **Token Validation Middleware** - Custom middleware for protecting routes
- **Frontend Integration** - Ready-to-use HTML/JavaScript frontend
- **Error Handling** - Comprehensive validation and error responses

## 📋 Requirements

- PHP 8.1+
- Laravel 11
- MySQL/MariaDB
- Composer

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd API_auth
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   Update your `.env` file with database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Start the server**
   ```bash
   php artisan serve
   ```

## 📊 Database Structure

### Users Table
- `id` (Primary Key)
- `name` (String)
- `email` (String, Unique)
- `password` (String, Hashed)
- `created_at` (Timestamp)
- `updated_at` (Timestamp)

### API Tokens Table
- `id` (Primary Key)
- `user_id` (Foreign Key to users.id)
- `token` (String, 64 characters)
- `created_at` (Timestamp)
- `updated_at` (Timestamp)

## 🔗 API Endpoints

### Public Endpoints (No Authentication Required)

#### Register User
```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (201):**
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    }
}
```

#### Login User
```http
POST /api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "message": "Login successful",
    "access_token": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6",
    "token_type": "Bearer",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    }
}
```

### Protected Endpoints (Authentication Required)

All protected endpoints require the `Authorization` header:
```http
Authorization: Bearer your_access_token_here
```

#### Get Current User
```http
GET /api/user
Authorization: Bearer your_access_token_here
```

**Response (200):**
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
}
```

#### Logout User
```http
POST /api/logout
Authorization: Bearer your_access_token_here
```

**Response (200):**
```json
{
    "message": "Logged out successfully"
}
```

## 🔐 Authentication Flow

1. **Registration**
   - User submits registration form
   - Server validates input and creates user
   - Password is hashed using bcrypt
   - Success response returned

2. **Login**
   - User submits email/password
   - Server validates credentials
   - Existing tokens for user are deleted (single session)
   - New random token is generated and stored
   - Token and user info returned

3. **Protected Requests**
   - Client includes `Authorization: Bearer <token>` header
   - Custom middleware validates token
   - User object attached to request
   - Request proceeds to controller

4. **Logout**
   - Client sends logout request with token
   - Server deletes token from database
   - Client clears token from storage

## 📁 File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── authController.php      # Main authentication controller
│   └── Middleware/
│       └── auth.php                # Custom authentication middleware
└── Models/
    ├── User.php                    # User model
    └── api_token.php               # API token model

resources/views/
├── register.blade.php              # Registration page
├── login.blade.php                 # Login page
└── dashboard.blade.php             # Protected dashboard

routes/
├── web.php                         # Web routes (views)
└── api.php                         # API routes

database/migrations/
├── create_users_table.php          # Users table migration
└── create_api_tokens_table.php     # API tokens table migration
```

## 🌐 Web Interface

The system includes a complete web interface:

- **Homepage/Register**: `/` or `/register` - User registration form
- **Login**: `/login` - User login form  
- **Dashboard**: `/dashboard` - Protected user dashboard

### Frontend Features

- **Token Management**: Automatic token storage in localStorage
- **Route Protection**: Auto-redirect to login for unauthenticated users
- **Error Handling**: User-friendly error messages
- **AJAX Integration**: All forms use fetch API for seamless experience

## 🔧 Configuration

### Custom Middleware

The `auth.custom` middleware is registered in `bootstrap/app.php`:

```php
$middleware->alias([
    'auth.custom' => \App\Http\Middleware\auth::class,
]);
```

### CSRF Protection

API routes are exempted from CSRF verification:

```php
$middleware->validateCsrfTokens(except: [
    'api/*',
]);
```

## 🚨 Error Responses

### Validation Errors (422)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 6 characters."]
    }
}
```

### Authentication Errors (401)
```json
{
    "error": "Token missing"
}
```

```json
{
    "error": "Invalid token"
}
```

```json
{
    "error": "Invalid credentials"
}
```

## 🔒 Security Features

- **Password Hashing**: All passwords are hashed using Laravel's Hash facade (bcrypt)
- **Token Security**: 64-character random tokens generated using `bin2hex(random_bytes(32))`
- **Single Session**: Old tokens are automatically deleted on new login
- **Input Validation**: All inputs are validated using Laravel's validation rules
- **SQL Injection Protection**: Eloquent ORM provides automatic protection

## 📝 Usage Examples

### JavaScript Frontend Integration

```javascript
// Register a new user
async function register(name, email, password) {
    const response = await fetch('/api/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ name, email, password })
    });
    
    return await response.json();
}

// Login user
async function login(email, password) {
    const response = await fetch('/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ email, password })
    });
    
    const data = await response.json();
    if (response.ok) {
        localStorage.setItem('token', data.access_token);
    }
    return data;
}

// Make authenticated request
async function getUser() {
    const token = localStorage.getItem('token');
    const response = await fetch('/api/user', {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    return await response.json();
}

// Logout
async function logout() {
    const token = localStorage.getItem('token');
    await fetch('/api/logout', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    localStorage.removeItem('token');
}
```

### cURL Examples

```bash
# Register
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123"}'

# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'

# Get User (replace TOKEN with actual token)
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer TOKEN"

# Logout
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer TOKEN"
```

## 🐛 Troubleshooting

### Common Issues

1. **"Token missing" error**
   - Ensure the Authorization header is properly formatted: `Bearer <token>`
   - Check that the token is being stored and retrieved correctly from localStorage

2. **"Invalid token" error**
   - Token may have been deleted from database
   - User may need to login again

3. **CSRF token mismatch**
   - API routes should be exempted from CSRF verification
   - Check `bootstrap/app.php` configuration

4. **Database connection errors**
   - Verify database credentials in `.env` file
   - Ensure database server is running
   - Run `php artisan migrate` to create tables

## 🚀 Quick Start

1. **Visit the application**: `http://localhost:8000`
2. **Register a new account** using the registration form
3. **Login** with your credentials to get an access token
4. **Access the dashboard** to see your user information
5. **Use the API endpoints** for integration with other applications

## 📜 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📞 Support

If you encounter any issues or have questions, please create an issue in the repository.
