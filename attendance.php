<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('staff_login.php');
}

$message = '';

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verifyCsrf();
    if ($_POST['action'] === 'mark') {
        $service_date = $_POST['service_date'];
        $service_type = $_POST['service_type'];
        
        // Delete existing attendance for this date using prepared statement
        $del = $conn->prepare("DELETE FROM attendance WHERE service_date = ? AND service_type = ?");
        $del->bind_param("ss", $service_date, $service_type);
        $del->execute();
        
        // Insert new attendance
        if (isset($_POST['members'])) {
            foreach ($_POST['members'] as $member_id) {
                $member_id = (int)$member_id;
                $stmt = $conn->prepare("INSERT INTO attendance (member_id, service_date, service_type) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $member_id, $service_date, $service_type);
                $stmt->execute();
            }
        }
        $message = '<div class="alert alert-success">Attendance marked successfully!</div>';
    }
}

// Get members
$members = $conn->query("SELECT * FROM members WHERE membership_status IN ('Active', 'New Convert') ORDER BY first_name");

// Get recent attendance
$recent_attendance = $conn->query("SELECT service_date, service_type, COUNT(*) as count FROM attendance GROUP BY service_date, service_type ORDER BY service_date DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - Church Management System</title>
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
                <h1 class="h2 mb-0"><i class="fas fa-clipboard-check me-2"></i>Attendance Tracking</h1>
            </div>
            
            <?php echo $message; ?>
            
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="card shadow-sm accent-gold">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Mark Attendance</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="action" value="mark">
                                
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Service Date *</label>
                                        <input type="date" name="service_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Service Type *</label>
                                        <select name="service_type" class="form-select" required>
                                            <option value="Sunday Service">Sunday Service</option>
                                            <option value="Midweek Service">Midweek Service</option>
                                            <option value="Prayer Meeting">Prayer Meeting</option>
                                            <option value="Bible Study">Bible Study</option>
                                            <option value="Special Event">Special Event</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" onclick="toggleAll(this)" id="selectAll">
                                        <label class="form-check-label" for="selectAll">Select All</label>
                                    </div>
                                </div>
                                
                                <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                    <?php while ($member = $members->fetch_assoc()): ?>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" name="members[]" value="<?php echo $member['id']; ?>" class="form-check-input member-checkbox" id="member<?php echo $member['id']; ?>">
                                        <label class="form-check-label" for="member<?php echo $member['id']; ?>">
                                            <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>
                                        </label>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                                
                                <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i>Save Attendance</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Attendance</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-calendar me-1"></i>Date</th>
                                            <th><i class="fas fa-church me-1"></i>Type</th>
                                            <th><i class="fas fa-users me-1"></i>Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($record = $recent_attendance->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($record['service_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($record['service_type']); ?></td>
                                            <td><strong><?php echo $record['count']; ?></strong></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleAll(source) {
            const checkboxes = document.querySelectorAll('.member-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = source.checked);
        }
    </script>
</body>
</html>
