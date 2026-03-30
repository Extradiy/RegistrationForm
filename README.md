# PHP Registration Form

A simple, single-file PHP registration system with MySQL database integration.

## Features

- ✅ Complete registration system in ONE file
- ✅ Automatic database and table creation
- ✅ Secure password hashing (bcrypt)
- ✅ Input validation and sanitization
- ✅ SQL injection protection (prepared statements)
- ✅ XSS protection
- ✅ Real-time form validation with visual feedback
- ✅ Client-side and server-side validation
- ✅ Password strength requirements with live indicators
- ✅ Inappropriate username filtering
- ✅ Form submission prevention until all requirements met
- ✅ Responsive modern design
- ✅ User-friendly error messages

## Requirements

- XAMPP (Apache + MySQL)
- PHP 7.4 or higher
- MySQL 5.7 or higher

## Installation

### Method 1: Automatic (Recommended for first-time setup)
1. Make sure XAMPP is installed and running
2. Start Apache and MySQL servers in XAMPP Control Panel
3. Access the form at: `http://localhost/Registrationform/RegistrationForm/registration.php`
4. The database and table will be created automatically on first run

### Method 2: Using Database Export (Recommended for team sharing)
1. Start XAMPP and make sure MySQL is running
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Click "Import" tab
4. Choose `database_export.sql` file from the repository
5. Click "Go" to import

**OR** use command line:
```bash
mysql -u root -p < database_export.sql
```

This ensures everyone on your team has the exact same database structure.

## Database Structure

**Database:** `registration_db`

**Table:** `users`

| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) UNSIGNED | Primary key, auto-increment |
| username | VARCHAR(50) | Unique username |
| email | VARCHAR(100) | Unique email address |
| password | VARCHAR(255) | Hashed password |
| fullname | VARCHAR(100) | User's full name |
| phone | VARCHAR(8) | Phone number (exactly 8 digits, required) |
| created_at | TIMESTAMP | Registration timestamp |

## Form Fields

### Required Fields
- **Full Name** - User's complete name
- **Username** - 3+ characters, must start with a letter, letters/numbers/underscores only, appropriate content required
- **Email** - Valid email format, must contain @
- **Phone Number** - Exactly 8 digits (numbers only)
- **Password** - Minimum 7 characters, must include:
  - At least one capital letter (A-Z)
  - At least one number (0-9)
  - At least one special character (!@#$%^&*...  )
- **Confirm Password** - Must match password

### Optional Fields
- None (all fields are required)

### Real-Time Validation
- ✅ Password requirements shown with live checkmarks
- ✅ Input fields turn green when valid, red when invalid
- ✅ Submit button disabled until all requirements met
- ✅ Instant feedback on every keystroke
- ✅ Form submission blocked if validation fails

## Validation Rules

- **Full Name**: Minimum 3 characters, letters and spaces only
- **Username**: Minimum 3 characters, must start with a letter, alphanumeric and underscores only, reserved and inappropriate names blocked
- **Email**: Must be valid email format and contain @ symbol
- **Password**: Minimum 7 characters with at least:
  - One capital letter (A-Z)
  - One number (0-9)
  - One special character (!@#$%^&*(),.?":{}|<>)
- **Confirm Password**: Must match the password field
- **Phone Number**: Required, exactly 8 digits (numbers only)
- Duplicate usernames/emails are rejected
- Real-time validation prevents submission of invalid data

## Security Features

- **Password Hashing:** Uses PHP's `password_hash()` with bcrypt
- **SQL Injection Prevention:** Prepared statements with parameterized queries
- **XSS Protection:** All outputs sanitized with `htmlspecialchars()`
- **Input Validation:** Server-side validation for all fields
- **Error Handling:** Graceful database connection error handling

## Database Configuration

Default settings (can be modified in registration.php):
```php
DB_HOST: localhost
DB_USER: root
DB_PASS: (empty)
DB_NAME: registration_db
```

## Troubleshooting

### "Connection refused" error
- Make sure MySQL is running in XAMPP Control Panel
- Check if MySQL is using the default port (3306)
- Verify database credentials in the file

### "Table doesn't exist" error
- The table should be created automatically
- If not, check MySQL user permissions

### Form doesn't submit
- Make sure Apache is running
- Check PHP error logs in XAMPP
- Verify file permissions

## File Structure

```
RegistrationForm/
├── registration.php      # Complete registration system (only file needed)
├── database_export.sql   # Database structure export for team sharing
└── README.md            # This file
```

## Usage

1. Open your browser
2. Navigate to `http://localhost/Registrationform/RegistrationForm/registration.php`
3. Fill in the registration form
4. Submit to create a new user account
5. Success message will appear with the user ID

## Viewing Registered Users

To view registered users, access phpMyAdmin:
1. Go to `http://localhost/phpmyadmin`
2. Select `registration_db` database
3. Click on `users` table
4. View all registered users

## License

Free to use and modify.

## Author

Created for XAMPP/PHP development practice.
