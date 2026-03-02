<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$message = '';

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'mark') {
        $service_date = $_POST['service_date'];
        $service_type = $_POST['service_type'];
        
        // Delete existing attendance for this date
        $conn->query("DELETE FROM attendance WHERE service_date = '$service_date' AND service_type = '$service_type'");
        
        // Insert new attendance
        if (isset($_POST['members'])) {
            foreach ($_POST['members'] as $member_id) {
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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <h1>Attendance Tracking</h1>
            
            <?php echo $message; ?>
            
            <div class="dashboard-grid">
                <div class="card">
                    <h2>Mark Attendance</h2>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="mark">
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Service Date *</label>
                                <input type="date" name="service_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Service Type *</label>
                                <select name="service_type" required>
                                    <option value="Sunday Service">Sunday Service</option>
                                    <option value="Midweek Service">Midweek Service</option>
                                    <option value="Prayer Meeting">Prayer Meeting</option>
                                    <option value="Bible Study">Bible Study</option>
                                    <option value="Special Event">Special Event</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" onclick="toggleAll(this)"> Select All
                            </label>
                        </div>
                        
                        <div style="max-height: 400px; overflow-y: auto; border: 2px solid #e2e8f0; border-radius: 8px; padding: 15px;">
                            <?php while ($member = $members->fetch_assoc()): ?>
                            <div style="padding: 8px; border-bottom: 1px solid #f0f0f0;">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" name="members[]" value="<?php echo $member['id']; ?>" class="member-checkbox" style="margin-right: 10px;">
                                    <span><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></span>
                                </label>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">Save Attendance</button>
                        </div>
                    </form>
                </div>
                
                <div class="card">
                    <h2>Recent Attendance</h2>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Service Type</th>
                                <th>Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($record = $recent_attendance->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($record['service_date'])); ?></td>
                                <td><?php echo $record['service_type']; ?></td>
                                <td><strong><?php echo $record['count']; ?></strong> members</td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function toggleAll(source) {
            const checkboxes = document.querySelectorAll('.member-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = source.checked);
        }
    </script>
</body>
</html>
