<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO groups (group_name, description, leader_id, meeting_day, meeting_time) VALUES (?, ?, ?, ?, ?)");
        $leader_id = $_POST['leader_id'] ?: null;
        $stmt->bind_param("ssiss", $_POST['group_name'], $_POST['description'], $leader_id, $_POST['meeting_day'], $_POST['meeting_time']);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Group created successfully!</div>';
        }
    }
}

// Get groups
$groups = $conn->query("SELECT g.*, CONCAT(m.first_name, ' ', m.last_name) as leader_name, (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count FROM groups g LEFT JOIN members m ON g.leader_id = m.id ORDER BY g.group_name");

// Get members for dropdown
$members = $conn->query("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM members ORDER BY first_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups - Church Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <h1>Groups & Ministries</h1>
            
            <?php echo $message; ?>
            
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>All Groups</h2>
                    <button onclick="openModal()" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Group
                    </button>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php while ($group = $groups->fetch_assoc()): ?>
                    <div style="background: #f7fafc; padding: 20px; border-radius: 12px; border-left: 4px solid #667eea;">
                        <h3 style="margin-bottom: 10px; color: #2d3748;"><?php echo htmlspecialchars($group['group_name']); ?></h3>
                        <p style="color: #666; font-size: 14px; margin-bottom: 15px;"><?php echo htmlspecialchars($group['description']); ?></p>
                        
                        <div style="display: flex; flex-direction: column; gap: 8px; font-size: 13px; color: #555;">
                            <div><i class="fas fa-user" style="width: 20px;"></i> Leader: <strong><?php echo htmlspecialchars($group['leader_name'] ?? 'Not assigned'); ?></strong></div>
                            <div><i class="fas fa-users" style="width: 20px;"></i> Members: <strong><?php echo $group['member_count']; ?></strong></div>
                            <?php if ($group['meeting_day']): ?>
                            <div><i class="fas fa-calendar" style="width: 20px;"></i> <?php echo $group['meeting_day']; ?> at <?php echo date('h:i A', strtotime($group['meeting_time'])); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div id="groupModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Create Group</h2>
                <button onclick="closeModal()" class="close-modal">&times;</button>
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label>Group Name *</label>
                    <input type="text" name="group_name" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"></textarea>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Leader</label>
                        <select name="leader_id">
                            <option value="">Select Leader</option>
                            <?php 
                            $members->data_seek(0);
                            while ($member = $members->fetch_assoc()): 
                            ?>
                            <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Meeting Day</label>
                        <select name="meeting_day">
                            <option value="">Select Day</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Meeting Time</label>
                        <input type="time" name="meeting_time">
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Create Group</button>
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openModal() {
            document.getElementById('groupModal').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('groupModal').classList.remove('active');
        }
    </script>
</body>
</html>
