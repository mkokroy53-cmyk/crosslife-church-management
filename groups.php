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
        $stmt = $conn->prepare("INSERT INTO `groups` (group_name, description, leader_id, meeting_day, meeting_time) VALUES (?, ?, ?, ?, ?)");
        $leader_id = $_POST['leader_id'] ?: null;
        $stmt->bind_param("ssiss", $_POST['group_name'], $_POST['description'], $leader_id, $_POST['meeting_day'], $_POST['meeting_time']);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Group created successfully!</div>';
        }
    }
}

// Get groups
$groups = $conn->query("SELECT g.*, CONCAT(m.first_name, ' ', m.last_name) as leader_name, (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count FROM `groups` g LEFT JOIN members m ON g.leader_id = m.id ORDER BY g.group_name");

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-users me-2"></i>Groups & Ministries</h1>
                <button onclick="openModal()" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Create Group
                </button>
            </div>
            
            <?php echo $message; ?>
            
            <div class="row g-4">
                <?php while ($group = $groups->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm h-100 accent-gold">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?php echo htmlspecialchars($group['group_name']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($group['description']); ?></p>
                            
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user me-2 text-secondary"></i>
                                    <small class="text-muted">Leader: <strong><?php echo htmlspecialchars($group['leader_name'] ?? 'Not assigned'); ?></strong></small>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-users me-2 text-secondary"></i>
                                    <small class="text-muted">Members: <strong><?php echo $group['member_count']; ?></strong></small>
                                </div>
                                <?php if ($group['meeting_day']): ?>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar me-2 text-secondary"></i>
                                    <small class="text-muted"><?php echo $group['meeting_day']; ?> at <?php echo date('h:i A', strtotime($group['meeting_time'])); ?></small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="groupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Create Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label for="group_name" class="form-label">Group Name *</label>
                            <input type="text" class="form-control" id="group_name" name="group_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="leader_id" class="form-label">Leader</label>
                                <select class="form-select" id="leader_id" name="leader_id">
                                    <option value="">Select Leader</option>
                                    <?php 
                                    $members->data_seek(0);
                                    while ($member = $members->fetch_assoc()): 
                                    ?>
                                    <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="meeting_day" class="form-label">Meeting Day</label>
                                <select class="form-select" id="meeting_day" name="meeting_day">
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
                            
                            <div class="col-12">
                                <label for="meeting_time" class="form-label">Meeting Time</label>
                                <input type="time" class="form-control" id="meeting_time" name="meeting_time">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openModal() {
            const modal = new bootstrap.Modal(document.getElementById('groupModal'));
            modal.show();
        }
    </script>
</body>
</html>
