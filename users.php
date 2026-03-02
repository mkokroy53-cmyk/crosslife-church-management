<?php
require_once 'config.php';

if (!isLoggedIn() || !hasRole('admin')) {
    redirect('dashboard.php');
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $_POST['username'], $hashed_password, $_POST['full_name'], $_POST['email'], $_POST['role']);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">User created successfully!</div>';
        } else {
            $message = '<div class="alert alert-error">Username already exists!</div>';
        }
    }
}

// Handle delete
if (isset($_GET['delete']) && $_GET['delete'] != $_SESSION['user_id']) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">User deleted successfully!</div>';
    }
}

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Church Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <h1>Users Management</h1>
            
            <?php echo $message; ?>
            
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>System Users</h2>
                    <button onclick="openModal()" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add User
                    </button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><span class="badge active"><?php echo ucfirst($user['role']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="?delete=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?')" class="action-btn delete">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add User</h2>
                <button onclick="closeModal()" class="close-modal">&times;</button>
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Username *</label>
                        <input type="text" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email">
                    </div>
                    
                    <div class="form-group">
                        <label>Role *</label>
                        <select name="role" required>
                            <option value="volunteer">Volunteer</option>
                            <option value="secretary">Secretary</option>
                            <option value="treasurer">Treasurer</option>
                            <option value="pastor">Pastor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Create User</button>
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openModal() {
            document.getElementById('userModal').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('userModal').classList.remove('active');
        }
    </script>
</body>
</html>
