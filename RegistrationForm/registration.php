<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'registration_db');

$errors = [];
$success = '';
$conn = null;

// Try to connect to database
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    // Create database if it doesn't exist
    $conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $conn->select_db(DB_NAME);
    
    // Create users table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fullname VARCHAR(100) NOT NULL,
        phone VARCHAR(8) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(username),
        INDEX(email)
    )";
    $conn->query($sql);
    
} catch (mysqli_sql_exception $e) {
    $errors[] = "Database connection failed. Please make sure MySQL is running in XAMPP.";
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $conn) {
    // Get and sanitize input
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $fullname = trim($_POST['fullname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    // Validation
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters";
    } elseif (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $username)) {
        $errors[] = "Username must start with a letter and contain only letters, numbers, and underscores";
    } elseif (preg_match('/^(admin|root|test|user|demo|null|undefined)$/i', $username)) {
        $errors[] = "This username is not allowed";
    } elseif (preg_match('/(fuck|shit|ass|damn|bitch|bastard|crap|piss|dick|cock|pussy|slut|whore|nigger|fag|sex|porn|xxx)/i', $username)) {
        $errors[] = "Username contains inappropriate content";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (strpos($email, '@') === false) {
        $errors[] = "Email must contain @ symbol";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 7) {
        $errors[] = "Password must be at least 7 characters";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one capital letter";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    } elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $errors[] = "Password must contain at least one special character (!@#$%^&*(),.?\":{}|<>)";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    if (empty($fullname)) {
        $errors[] = "Full name is required";
    } elseif (strlen($fullname) < 3) {
        $errors[] = "Full name must be at least 3 characters";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $fullname)) {
        $errors[] = "Full name can only contain letters and spaces";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    } elseif (!preg_match('/^[0-9]{8}$/', $phone)) {
        $errors[] = "Phone number must be exactly 8 digits";
    }
    
    // If no errors, try to register user
    if (empty($errors)) {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Username or email already exists";
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, fullname, phone, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssss", $username, $email, $hashed_password, $fullname, $phone);
            
            if ($stmt->execute()) {
                $success = "Registration successful! User ID: " . $stmt->insert_id;
                // Clear form fields
                $username = $email = $fullname = $phone = '';
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
        }
        $stmt->close();
    }
}

if ($conn) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 32px;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
        }
        
        label .required {
            color: #e74c3c;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            font-family: inherit;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        input:hover {
            border-color: #c0c0c0;
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .error-box {
            background: #fee;
            border-left: 4px solid #e74c3c;
            color: #c33;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .error-box strong {
            display: block;
            margin-bottom: 8px;
            font-size: 15px;
        }
        
        .error-box ul {
            list-style: none;
            padding-left: 0;
        }
        
        .error-box li {
            margin-bottom: 5px;
            padding-left: 20px;
            position: relative;
        }
        
        .error-box li:before {
            content: "✕";
            position: absolute;
            left: 0;
            font-weight: bold;
        }
        
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
        }
        
        .success-box:before {
            content: "✓";
            display: inline-block;
            margin-right: 8px;
            font-weight: bold;
            font-size: 18px;
        }
        
        .password-hint {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }
        
        .validation-feedback {
            font-size: 12px;
            margin-top: 8px;
            padding: 10px;
            border-radius: 5px;
            background: #f8f9fa;
        }
        
        .requirement {
            margin: 5px 0;
            padding-left: 20px;
            position: relative;
        }
        
        .requirement:before {
            content: "✕";
            position: absolute;
            left: 0;
            color: #e74c3c;
            font-weight: bold;
        }
        
        .requirement.valid:before {
            content: "✓";
            color: #28a745;
        }
        
        input.invalid {
            border-color: #e74c3c !important;
        }
        
        input.valid {
            border-color: #28a745 !important;
        }
        
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 Registration Form</h1>
        <p class="subtitle">Create your account</p>
        
        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <strong>Please fix the following errors:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-box">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="fullname">
                    Full Name <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="fullname" 
                    name="fullname" 
                    value="<?php echo htmlspecialchars($fullname ?? ''); ?>" 
                    placeholder="Enter your full name"
                    minlength="3"
                    pattern="[a-zA-Z\s]+"
                    title="Full name must be at least 3 characters and contain only letters and spaces"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="username">
                    Username <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="<?php echo htmlspecialchars($username ?? ''); ?>" 
                    placeholder="Choose a username"
                    minlength="3"
                    pattern="[a-zA-Z][a-zA-Z0-9_]*"
                    title="Username must start with a letter and be at least 3 characters"
                    required
                >
                <div class="password-hint">Must start with a letter, min 3 characters, appropriate content only</div>
            </div>
            
            <div class="form-group">
                <label for="email">
                    Email Address <span class="required">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                    placeholder="your@email.com"
                    pattern=".*@.*"
                    title="Email must contain @ symbol"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="phone">
                    Phone Number <span class="required">*</span>
                </label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    value="<?php echo htmlspecialchars($phone ?? ''); ?>" 
                    placeholder="12345678 (8 digits)"
                    maxlength="8"
                    pattern="[0-9]{8}"
                    required
                >
                <div class="password-hint">Exactly 8 digits required</div>
            </div>
            
            <div class="form-group">
                <label for="password">
                    Password <span class="required">*</span>
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter password"
                    minlength="7"
                    required
                >
                <div class="validation-feedback" id="password-feedback">
                    <div class="requirement" id="req-length">At least 7 characters</div>
                    <div class="requirement" id="req-capital">One capital letter (A-Z)</div>
                    <div class="requirement" id="req-number">One number (0-9)</div>
                    <div class="requirement" id="req-special">One special character (!@#$%^&*...)</div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">
                    Confirm Password <span class="required">*</span>
                </label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    placeholder="Re-enter password"
                    required
                >
            </div>
            
            <button type="submit" class="btn" id="submit-btn">Register Now</button>
        </form>
    </div>
    
    <script>
        const form = document.querySelector('form');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const submitBtn = document.getElementById('submit-btn');
        const username = document.getElementById('username');
        const email = document.getElementById('email');
        const fullname = document.getElementById('fullname');
        
        // Password validation requirements
        const requirements = {
            length: { regex: /.{7,}/, element: document.getElementById('req-length') },
            capital: { regex: /[A-Z]/, element: document.getElementById('req-capital') },
            number: { regex: /[0-9]/, element: document.getElementById('req-number') },
            special: { regex: /[!@#$%^&*(),.?":{}|<>]/, element: document.getElementById('req-special') }
        };
        
        // Check password requirements
        function checkPassword() {
            const value = password.value;
            let allValid = true;
            
            for (let key in requirements) {
                if (requirements[key].regex.test(value)) {
                    requirements[key].element.classList.add('valid');
                } else {
                    requirements[key].element.classList.remove('valid');
                    allValid = false;
                }
            }
            
            if (value.length > 0) {
                password.classList.toggle('valid', allValid);
                password.classList.toggle('invalid', !allValid);
            } else {
                password.classList.remove('valid', 'invalid');
            }
            
            return allValid;
        }
        
        // Check if passwords match
        function checkPasswordMatch() {
            if (confirmPassword.value.length > 0) {
                const match = password.value === confirmPassword.value;
                confirmPassword.classList.toggle('valid', match);
                confirmPassword.classList.toggle('invalid', !match);
                return match;
            }
            confirmPassword.classList.remove('valid', 'invalid');
            return false;
        }
        
        // Validate username
        function checkUsername() {
            const value = username.value;
            const inappropriateWords = /(fuck|shit|ass|damn|bitch|bastard|crap|piss|dick|cock|pussy|slut|whore|nigger|fag|sex|porn|xxx)/i;
            const valid = value.length >= 3 && /^[a-zA-Z][a-zA-Z0-9_]*$/.test(value) && !inappropriateWords.test(value);
            
            if (value.length > 0) {
                username.classList.toggle('valid', valid);
                username.classList.toggle('invalid', !valid);
            } else {
                username.classList.remove('valid', 'invalid');
            }
            return valid;
        }
        
        // Validate email
        function checkEmail() {
            const value = email.value;
            const valid = value.includes('@') && value.length > 0;
            
            if (value.length > 0) {
                email.classList.toggle('valid', valid);
                email.classList.toggle('invalid', !valid);
            } else {
                email.classList.remove('valid', 'invalid');
            }
            return valid;
        }
        
        // Validate full name
        function checkFullname() {
            const value = fullname.value;
            const valid = value.length >= 3 && /^[a-zA-Z\s]+$/.test(value);
            
            if (value.length > 0) {
                fullname.classList.toggle('valid', valid);
                fullname.classList.toggle('invalid', !valid);
            } else {
                fullname.classList.remove('valid', 'invalid');
            }
            return valid;
        }
        
        // Validate entire form
        function validateForm() {
            const passwordValid = checkPassword();
            const passwordMatch = checkPasswordMatch();
            const usernameValid = checkUsername();
            const emailValid = checkEmail();
            const fullnameValid = checkFullname();
            const phoneValid = document.getElementById('phone').value.length === 8;
            
            const formValid = passwordValid && passwordMatch && usernameValid && emailValid && fullnameValid && phoneValid;
            
            submitBtn.disabled = !formValid;
            return formValid;
        }
        
        // Event listeners
        password.addEventListener('input', validateForm);
        confirmPassword.addEventListener('input', validateForm);
        username.addEventListener('input', validateForm);
        email.addEventListener('input', validateForm);
        fullname.addEventListener('input', validateForm);
        document.getElementById('phone').addEventListener('input', validateForm);
        
        // Prevent form submission if invalid
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                alert('Please fill in all required fields correctly before submitting.');
                return false;
            }
        });
        
        // Initial validation
        validateForm();
    </script>
</body>
</html>
