<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('staff_login.php');
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verifyCsrf();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave me-2"></i>Expenses Management</h1>
                <button onclick="openModal()" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Record Expense
                </button>
            </div>
            
            <?php echo $message; ?>
            
            <div class="card shadow-sm accent-gold">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Expenses</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-calendar me-1"></i>Date</th>
                                    <th><i class="fas fa-file-alt me-1"></i>Description</th>
                                    <th><i class="fas fa-tag me-1"></i>Category</th>
                                    <th><i class="fas fa-dollar-sign me-1"></i>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($expense = $expenses->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($expense['expense_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                    <td><?php echo htmlspecialchars($expense['category']); ?></td>
                                    <td><strong class="text-danger">-KSh <?php echo number_format($expense['amount'], 2); ?></strong></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="expenseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Record Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount *</label>
                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="category" class="form-label">Category *</label>
                                <select class="form-select" id="category" name="category" required>
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
                            
                            <div class="col-12">
                                <label for="expense_date" class="form-label">Date *</label>
                                <input type="date" class="form-control" id="expense_date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openModal() {
            const modal = new bootstrap.Modal(document.getElementById('expenseModal'));
            modal.show();
        }
    </script>
</body>
</html>
