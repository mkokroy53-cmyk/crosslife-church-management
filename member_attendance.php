<?php
require_once 'config.php';

if (!isset($_SESSION['member_id'])) {
    header('Location: member_login.php');
    exit;
}

$member_id = $_SESSION['member_id'];

// Get my attendance records using prepared statement
$stmt = $conn->prepare("SELECT * FROM attendance WHERE member_id = ? ORDER BY service_date DESC");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$attendance = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendance - Member Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/member_sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/member_header.php'; ?>
        
        <div class="content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 mb-0"><i class="fas fa-clipboard-check me-2"></i>My Attendance History</h1>
                <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print me-1"></i>Print Report</button>
            </div>
            
            <div class="card shadow-sm accent-gold">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-calendar me-1"></i>Date</th>
                                    <th><i class="fas fa-church me-1"></i>Service Type</th>
                                    <th><i class="fas fa-check-circle me-1"></i>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($record = $attendance->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($record['service_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($record['service_type']); ?></td>
                                    <td><span class="badge bg-success">Present</span></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
