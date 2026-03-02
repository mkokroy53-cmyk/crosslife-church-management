<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <h1>Contributions Management</h1>
            
            <?php echo $message; ?>
            
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Recent Contributions</h2>
                    <button onclick="openModal()" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Record Contribution
                    </button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Member</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($contribution = $contributions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($contribution['contribution_date'])); ?></td>
                            <td><?php echo htmlspecialchars($contribution['member_name'] ?? 'Anonymous'); ?></td>
                            <td><?php echo $contribution['contribution_type']; ?></td>
                            <td><strong>$<?php echo number_format($contribution['amount'], 2); ?></strong></td>
                            <td><?php echo $contribution['payment_method']; ?></td>
                            <td><?php echo htmlspecialchars($contribution['notes']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
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
                <input type="hidden" name="action" value="add">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Member</label>
                        <select name="member_id">
                            <option value="">Anonymous</option>
                            <?php 
                            $members->data_seek(0);
                            while ($member = $members->fetch_assoc()): 
                            ?>
                            <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Amount *</label>
                        <input type="number" name="amount" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Type *</label>
                        <select name="contribution_type" required>
                            <option value="Tithe">Tithe</option>
                            <option value="Offering">Offering</option>
                            <option value="Building Fund">Building Fund</option>
                            <option value="Missions">Missions</option>
                            <option value="Special Project">Special Project</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Payment Method *</label>
                        <select name="payment_method" required>
                            <option value="Cash">Cash</option>
                            <option value="Check">Check</option>
                            <option value="Online">Online</option>
                            <option value="Mobile Money">Mobile Money</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Date *</label>
                        <input type="date" name="contribution_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes"></textarea>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Save Contribution</button>
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
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
