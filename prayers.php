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
        $stmt = $conn->prepare("INSERT INTO prayer_requests (member_id, request, status) VALUES (?, ?, 'Open')");
        $member_id = $_POST['member_id'] ?: null;
        $stmt->bind_param("is", $member_id, $_POST['request']);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Prayer request added successfully!</div>';
        }
    } elseif ($_POST['action'] === 'update_status') {
        $stmt = $conn->prepare("UPDATE prayer_requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $_POST['status'], $_POST['id']);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Status updated successfully!</div>';
        }
    }
}

// Get prayer requests
$prayers = $conn->query("SELECT p.*, CONCAT(m.first_name, ' ', m.last_name) as member_name FROM prayer_requests p LEFT JOIN members m ON p.member_id = m.id ORDER BY p.created_at DESC");

// Get members for dropdown
$members = $conn->query("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM members ORDER BY first_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prayer Requests - Church Management System</title>
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
                <h1 class="h2 mb-0"><i class="fas fa-praying-hands me-2"></i>Prayer Requests</h1>
                <button onclick="openModal()" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Prayer Request</button>
            </div>
            
            <?php echo $message; ?>
            
            <div class="row g-4">
                <?php while ($prayer = $prayers->fetch_assoc()): ?>
                <div class="col-md-6">
                    <div class="card shadow-sm border-start border-4 accent-gold <?php 
                        echo $prayer['status'] === 'Answered' ? 'border-success' : ($prayer['status'] === 'Open' ? 'border-primary' : 'border-secondary'); 
                    ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="card-title mb-0"><?php echo htmlspecialchars($prayer['member_name'] ?? 'Anonymous'); ?></h6>
                                    <small class="text-muted"><?php echo date('M d, Y', strtotime($prayer['created_at'])); ?></small>
                                </div>
                                <span class="badge bg-<?php 
                                    echo $prayer['status'] === 'Answered' ? 'success' : ($prayer['status'] === 'Open' ? 'primary' : 'secondary'); 
                                ?>"><?php echo $prayer['status']; ?></span>
                            </div>
                            
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($prayer['request'])); ?></p>
                            
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="id" value="<?php echo $prayer['id']; ?>">
                                <select name="status" onchange="this.form.submit()" class="form-select form-select-sm d-inline-block w-auto">
                                    <option value="Open" <?php echo $prayer['status'] === 'Open' ? 'selected' : ''; ?>>Open</option>
                                    <option value="Answered" <?php echo $prayer['status'] === 'Answered' ? 'selected' : ''; ?>>Answered</option>
                                    <option value="Closed" <?php echo $prayer['status'] === 'Closed' ? 'selected' : ''; ?>>Closed</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div id="prayerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add Prayer Request</h2>
                <button onclick="closeModal()" class="close-modal">&times;</button>
            </div>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label>Member (Optional)</label>
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
                    <label>Prayer Request *</label>
                    <textarea name="request" rows="6" required></textarea>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openModal() {
            document.getElementById('prayerModal').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('prayerModal').classList.remove('active');
        }
    </script>
</body>
</html>
