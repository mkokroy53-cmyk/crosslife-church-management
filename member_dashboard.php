<?php
require_once 'config.php';

if (!isset($_SESSION['member_id'])) {
    redirect('member_login.php');
}

$member_id = (int)$_SESSION['member_id'];

// Get member info
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

// Get statistics
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM attendance WHERE member_id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$my_attendance = $stmt->get_result()->fetch_assoc()['count'];

$stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM contributions WHERE member_id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$my_contributions = $stmt->get_result()->fetch_assoc()['total'];

$upcoming_events = $conn->query("SELECT COUNT(*) as count FROM events WHERE event_date >= CURDATE()")->fetch_assoc()['count'];

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM prayer_requests WHERE member_id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$my_prayers = $stmt->get_result()->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard - Church Management System</title>
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
                <h1 class="h2 mb-0"><i class="fas fa-tachometer-alt me-2"></i>Welcome, <?php echo htmlspecialchars($member['first_name']); ?>!</h1>
                <small class="text-muted">Member Dashboard</small>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-clipboard-check fa-2x mb-2"></i>
                            <h3 class="card-title"><?php echo $my_attendance; ?></h3>
                            <p class="card-text">My Attendance</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-success h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-hand-holding-usd fa-2x mb-2"></i>
                            <h3 class="card-title">KSh <?php echo number_format($my_contributions, 2); ?></h3>
                            <p class="card-text">My Contributions</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-warning h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                            <h3 class="card-title"><?php echo $upcoming_events; ?></h3>
                            <p class="card-text">Upcoming Events</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-info h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-praying-hands fa-2x mb-2"></i>
                            <h3 class="card-title"><?php echo $my_prayers; ?></h3>
                            <p class="card-text">My Prayer Requests</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card shadow-sm accent-gold">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>My Profile</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($member['email']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($member['phone']); ?></p>
                            <p><strong>Status:</strong> <span class="badge bg-<?php echo strtolower($member['membership_status']) == 'active' ? 'success' : 'secondary'; ?>"><?php echo $member['membership_status']; ?></span></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card shadow-sm accent-gold">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Upcoming Events</h5>
                        </div>
                        <div class="card-body">
                            <div class="event-list">
                        <?php
                        $events = $conn->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date LIMIT 5");
                        while ($event = $events->fetch_assoc()):
                        ?>
                        <div class="event-item">
                            <div class="event-date">
                                <span class="day"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                                <span class="month"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                            </div>
                            <div class="event-info">
                                <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                                <p><?php echo htmlspecialchars($event['location']); ?></p>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
