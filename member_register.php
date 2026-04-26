<?php
require_once 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : NULL;
    $marital_status = !empty($_POST['marital_status']) ? $_POST['marital_status'] : NULL;
    $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : NULL;
    
    $stmt = $conn->prepare("INSERT INTO members (first_name, last_name, email, phone, address, date_of_birth, gender, marital_status, membership_status, join_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Visitor', CURDATE())");
    $stmt->bind_param("ssssssss", $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['address'], $date_of_birth, $gender, $marital_status);
    
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Registration successful! You can now <a href="member_login.php">login</a> with your email and phone.</div>';
    } else {
        $message = '<div class="alert alert-error">Registration failed. Please try again.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Registration - Church Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .register-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            color: #667eea;
            font-size: 28px;
            margin-bottom: 5px;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #c6f6d5;
            color: #22543d;
        }
        .alert-error {
            background: #fed7d7;
            color: #742a2a;
        }
        .alert a {
            color: inherit;
            font-weight: bold;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <h1>⛪ International Crosslife Church</h1>
            <p>New Member Registration</p>
        </div>
        
        <?php echo $message; ?>
        
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ">
            <div class="form-grid">
                <div class="form-group">
                    <label>First Name *</label>
                    <input type="text" name="first_name" required>
                </div>
                
                <div class="form-group">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" required>
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Phone *</label>
                    <input type="text" name="phone" required placeholder="e.g., 0712345678">
                </div>
                
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="date_of_birth">
                </div>
                
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender">
                        <option value="">Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Marital Status</label>
                    <select name="marital_status">
                        <option value="">Select</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Widowed">Widowed</option>
                        <option value="Divorced">Divorced</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="3"></textarea>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </form>
        
        <div class="links">
            <a href="member_login.php">Already registered? Login here</a>
        </div>
    </div>
</body>
</html>
