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
        $stmt = $conn->prepare("INSERT INTO contributions (member_id, amount, contribution_type, payment_method, contribution_date, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("idssss", $_POST['member_id'], $_POST['amount'], $_POST['contribution_type'], $_POST['payment_method'], $_POST['contribution_date'], $_POST['notes']);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Contribution recorded successfully!</div>';
        }
    }
}

// Get contributions
$contributions = $conn->query("SELECT c.*, CONCAT(m.first_name, ' ', m.last_name) as member_name FROM contributions c LEFT JOIN members m ON c.member_id = m.id ORDER BY c.contribution_date DESC LIMIT 100");

// Get members for dropdown
$members = $conn->query("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM members ORDER BY first_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contributions - Church Management System</title>
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
                <h1 class="h2 mb-0"><i class="fas fa-hand-holding-usd me-2"></i>Contributions Management</h1>
                <button onclick="openModal()" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Record Contribution</button>
            </div>
            
            <?php echo $message; ?>
            
            <div class="card shadow-sm accent-gold">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Contributions</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-calendar me-1"></i>Date</th>
                                    <th><i class="fas fa-user me-1"></i>Member</th>
                                    <th><i class="fas fa-tag me-1"></i>Type</th>
                                    <th><i class="fas fa-money-bill me-1"></i>Amount</th>
                                    <th><i class="fas fa-credit-card me-1"></i>Payment</th>
                                    <th><i class="fas fa-sticky-note me-1"></i>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($contribution = $contributions->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($contribution['contribution_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($contribution['member_name'] ?? 'Anonymous'); ?></td>
                                    <td><?php echo htmlspecialchars($contribution['contribution_type']); ?></td>
                                    <td><strong>KSh <?php echo number_format($contribution['amount'], 2); ?></strong></td>
                                    <td><?php echo htmlspecialchars($contribution['payment_method']); ?></td>
                                    <td><?php echo htmlspecialchars($contribution['notes']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div id="contributionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Record Contribution</h2>
                <button onclick="closeModal()" class="close-modal">&times;</button>
            </div>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="add">
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Member</label>
                        <select name="member_id" class="form-select">
                            <option value="">Anonymous</option>
                            <?php 
                            $members->data_seek(0);
                            while ($member = $members->fetch_assoc()): 
                            ?>
                            <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Amount *</label>
                        <input type="number" name="amount" class="form-control" step="0.01" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Type *</label>
                        <select name="contribution_type" class="form-select" required>
                            <option value="Tithe">Tithe</option>
                            <option value="Offering">Offering</option>
                            <option value="Building Fund">Building Fund</option>
                            <option value="Missions">Missions</option>
                            <option value="Special Project">Special Project</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Payment Method *</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="Cash">Cash</option>
                            <option value="Check">Check</option>
                            <option value="Online">Online</option>
                            <option value="Mobile Money">Mobile Money</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Date *</label>
                        <input type="date" name="contribution_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>
                </div>
                
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Contribution</button>
                    <button type="button" onclick="closeModal()" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openModal() {
            document.getElementById('contributionModal').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('contributionModal').classList.remove('active');
        }
    </script>
</body>
</html>
