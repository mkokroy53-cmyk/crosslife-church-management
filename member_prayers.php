<?php
require_once 'config.php';

if (!isset($_SESSION['member_id'])) {
    redirect('member_login.php');
}

$member_id = (int)$_SESSION['member_id'];
$message = '';

// Handle prayer request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verifyCsrf();
    if ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO prayer_requests (member_id, request, status) VALUES (?, ?, 'Open')");
        $stmt->bind_param("is", $member_id, $_POST['request']);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Prayer request submitted successfully!</div>';
        }
    }
}

// Get my prayer requests
$stmt = $conn->prepare("SELECT * FROM prayer_requests WHERE member_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$prayers = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prayer Requests - Member Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/member_sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/member_header.php'; ?>
        
        <div class="content">
            <h1>My Prayer Requests</h1>
            
            <?php echo $message; ?>
            
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>All My Requests</h2>
                    <button onclick="openModal()" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Submit Prayer Request
                    </button>
                </div>
                
                <div style="display: grid; gap: 15px;">
                    <?php while ($prayer = $prayers->fetch_assoc()): ?>
                    <div style="background: #f7fafc; padding: 20px; border-radius: 12px; border-left: 4px solid <?php echo $prayer['status'] === 'Answered' ? '#48bb78' : ($prayer['status'] === 'Open' ? '#667eea' : '#cbd5e0'); ?>;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                            <span style="color: #666; font-size: 13px;">
                                <?php echo date('M d, Y', strtotime($prayer['created_at'])); ?>
                            </span>
                            <span class="badge" style="<?php 
                                if ($prayer['status'] === 'Open') echo 'background: #bee3f8; color: #2c5282;';
                                elseif ($prayer['status'] === 'Answered') echo 'background: #c6f6d5; color: #22543d;';
                                else echo 'background: #e2e8f0; color: #4a5568;';
                            ?>"><?php echo $prayer['status']; ?></span>
                        </div>
                        
                        <p style="color: #555;"><?php echo nl2br(htmlspecialchars($prayer['request'])); ?></p>
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
                <h2>Submit Prayer Request</h2>
                <button onclick="closeModal()" class="close-modal">&times;</button>
            </div>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="add">
                
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
