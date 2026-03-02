<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <h1>Prayer Requests</h1>
            
            <?php echo $message; ?>
            
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>All Prayer Requests</h2>
                    <button onclick="openModal()" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Prayer Request
                    </button>
                </div>
                
                <div style="display: grid; gap: 15px;">
                    <?php while ($prayer = $prayers->fetch_assoc()): ?>
                    <div style="background: #f7fafc; padding: 20px; border-radius: 12px; border-left: 4px solid <?php echo $prayer['status'] === 'Answered' ? '#48bb78' : ($prayer['status'] === 'Open' ? '#667eea' : '#cbd5e0'); ?>;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                            <div>
                                <strong style="color: #2d3748;"><?php echo htmlspecialchars($prayer['member_name'] ?? 'Anonymous'); ?></strong>
                                <span style="color: #666; font-size: 13px; margin-left: 10px;">
                                    <?php echo date('M d, Y', strtotime($prayer['created_at'])); ?>
                                </span>
                            </div>
                            <span class="badge <?php echo strtolower($prayer['status']); ?>" style="<?php 
                                if ($prayer['status'] === 'Open') echo 'background: #bee3f8; color: #2c5282;';
                                elseif ($prayer['status'] === 'Answered') echo 'background: #c6f6d5; color: #22543d;';
                                else echo 'background: #e2e8f0; color: #4a5568;';
                            ?>"><?php echo $prayer['status']; ?></span>
                        </div>
                        
                        <p style="color: #555; margin-bottom: 15px;"><?php echo nl2br(htmlspecialchars($prayer['request'])); ?></p>
                        
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="id" value="<?php echo $prayer['id']; ?>">
                            <select name="status" onchange="this.form.submit()" style="padding: 5px 10px; border-radius: 6px; border: 2px solid #e2e8f0; font-size: 13px;">
                                <option value="Open" <?php echo $prayer['status'] === 'Open' ? 'selected' : ''; ?>>Open</option>
                                <option value="Answered" <?php echo $prayer['status'] === 'Answered' ? 'selected' : ''; ?>>Answered</option>
                                <option value="Closed" <?php echo $prayer['status'] === 'Closed' ? 'selected' : ''; ?>>Closed</option>
                            </select>
                        </form>
                    </div>
                    <?php endwhile; ?>
                </div>
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
