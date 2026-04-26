<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password, full_name, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Verify password (assuming passwords are hashed)
        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            redirect('dashboard.php');
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - Church Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
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

        .login-card {
            background: rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 36px;
            box-shadow: 0 35px 100px rgba(0, 0, 0, 0.32);
            max-width: 460px;
            width: 100%;
            overflow: hidden;
        }

        .login-header {
            text-align: center;
            padding: 2.5rem 1.75rem 1rem;
            position: relative;
        }

        .login-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top center, rgba(255,255,255,0.18), transparent 42%);
            pointer-events: none;
        }

        .login-header i {
            font-size: 3rem;
            color: #ffffff;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .login-header h2 {
            color: #ffffff;
            font-weight: 800;
            font-size: 2.4rem;
            line-height: 1.05;
            margin-bottom: 0.3rem;
            position: relative;
            z-index: 1;
        }

        .login-header p {
            color: rgba(255, 255, 255, 0.82);
            font-size: 0.96rem;
            position: relative;
            z-index: 1;
        }

        .login-body {
            padding: 2rem 1.75rem 1.75rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.95rem;
            font-weight: 600;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.20);
            color: #ffffff;
            border-radius: 16px;
            padding: 1.25rem 1rem;
            height: auto;
        }

        .form-control:focus {
            border-color: rgba(76, 175, 80, 0.95);
            box-shadow: 0 0 0 0.25rem rgba(56, 239, 125, 0.18);
            background: rgba(255, 255, 255, 0.22);
            color: #ffffff;
        }

        .form-floating label {
            color: rgba(255, 255, 255, 0.75);
            left: 18px;
            top: 14px;
        }

        .btn-login {
            background: linear-gradient(135deg, #38ef7d 0%, #11998e 100%);
            border: none;
            padding: 1rem 1.5rem;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 16px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            color: white;
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 30px rgba(56, 239, 125, 0.3);
        }

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.15rem;
            border-top: 1px solid rgba(255,255,255,0.12);
        }

        .back-link a {
            color: rgba(255, 255, 255, 0.82);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #f8f9fa;
        }

        .alert {
            border-radius: 14px;
            border: none;
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }

        .alert i {
            color: #ffb703;
        }

        @media (max-width: 576px) {
            .login-card {
                margin: 1rem;
                border-radius: 20px;
            }

            .login-header {
                padding: 1.5rem 1rem 1rem;
            }

            .login-body {
                padding: 1.5rem 1rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
        <div class="login-card card">
            <div class="login-header">
                <i class="fas fa-user-shield"></i>
                <h2>Staff Login</h2>
                <p class="text-muted mb-0">Access the Church Management System</p>
            </div>

            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="form-group mb-4">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" class="btn btn-login w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>
            </div>

            <div class="back-link">
                <a href="welcome.php">
                    <i class="fas fa-arrow-left me-1"></i>Back to Portal
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>