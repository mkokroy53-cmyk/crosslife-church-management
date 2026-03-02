<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO expenses (description, amount, category, expense_date, recorded_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssi", $_POST['description'], $_POST['amount'], $_POST['category'], $_POST['expense_date'], $_SESSION['user_id']);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Expense recorded successfully!</div>';
        }
    }
}

// Get expenses
$expenses = $conn->query("SELECT * FROM expenses ORDER BY expense_date DESC LIMIT 100");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses - Church Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <h1>Expenses Management</h1>
            
            <?php echo $message; ?>
            
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Recent Expenses</h2>
                    <button onclick="openModal()" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Record Expense
                    </button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($expense = $expenses->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($expense['expense_date'])); ?></td>
                            <td><?php echo htmlspecialchars($expense['description']); ?></td>
                            <td><?php echo htmlspecialchars($expense['category']); ?></td>
                            <td><strong style="color: #f56565;">-$<?php echo number_format($expense['amount'], 2); ?></strong></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div id="expenseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Record Expense</h2>
                <button onclick="closeModal()" class="close-modal">&times;</button>
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label>Description *</label>
                    <input type="text" name="description" required>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Amount *</label>
                        <input type="number" name="amount" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" required>
                            <option value="">Select Category</option>
                            <option value="Utilities">Utilities</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Salaries">Salaries</option>
                            <option value="Supplies">Supplies</option>
                            <option value="Events">Events</option>
                            <option value="Missions">Missions</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Date *</label>
                        <input type="date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Save Expense</button>
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openModal() {
            document.getElementById('expenseModal').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('expenseModal').classList.remove('active');
        }
    </script>
</body>
</html>
