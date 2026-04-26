<?php
require_once 'config.php';

if (isLoggedIn() && isset($_SESSION['member_id'])) {
    redirect('member_dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, phone FROM members WHERE email = ? AND phone = ?");
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($member = $result->fetch_assoc()) {
        session_regenerate_id(true);
        $_SESSION['member_id'] = $member['id'];
        $_SESSION['member_name'] = $member['first_name'] . ' ' . $member['last_name'];
        $_SESSION['is_member'] = true;
        redirect('member_dashboard.php');
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Portal - Church Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.30), rgba(0, 0, 0, 0.20)),
                        url('cro.jpg') center center / cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #f8f9fa;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.35);
            width: 100%;
            max-width: 420px;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            color: #ffffff;
            font-size: 28px;
            margin-bottom: 5px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .logo p {
            color: rgba(255, 255, 255, 0.78);
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
            color: #ffffff;
        }
        input::placeholder {
            color: rgba(255, 255, 255, 0.55);
        }
        input:focus {
            outline: none;
            border-color: rgba(142, 90, 255, 0.95);
            background: rgba(255, 255, 255, 0.18);
            box-shadow: 0 0 0 0.25rem rgba(142, 90, 255, 0.2);
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #38ef7d 0%, #11998e 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 28px rgba(56, 239, 125, 0.35);
        }
        .error {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 107, 107, 0.6);
            color: #ffcccb;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .links {
            margin-top: 20px;
            text-align: center;
        }
        .links a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }
        .links a:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>⛪ International Crosslife Church</h1>
            <p>Member Portal</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required autofocus>
            </div>
            
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" required placeholder="e.g., 0712345678">
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="links">
            <a href="member_register.php">New Member? Register Here</a><br><br>
            <a href="welcome.php">← Back to Home</a>
        </div>
    </div>
</body>
</html>
