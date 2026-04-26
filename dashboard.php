<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('staff_login.php');
}

// Get statistics
$total_members = $conn->query("SELECT COUNT(*) as count FROM members")->fetch_assoc()['count'];
$active_members = $conn->query("SELECT COUNT(*) as count FROM members WHERE membership_status = 'Active'")->fetch_assoc()['count'];
$total_contributions = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM contributions WHERE MONTH(contribution_date) = MONTH(CURRENT_DATE())")->fetch_assoc()['total'];
$upcoming_events = $conn->query("SELECT COUNT(*) as count FROM events WHERE event_date >= CURDATE()")->fetch_assoc()['count'];
$recent_attendance = $conn->query("SELECT COUNT(DISTINCT member_id) as count FROM attendance WHERE service_date = (SELECT MAX(service_date) FROM attendance)")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Church Management System</title>
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
                <h1 class="h2 mb-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h1>
                <small class="text-muted">Admin Overview</small>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-md-2">
                    <div class="card text-white bg-primary h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3 class="card-title"><?php echo $total_members; ?></h3>
                            <p class="card-text">Total Members</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="card text-white bg-success h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-user-check fa-2x mb-2"></i>
                            <h3 class="card-title"><?php echo $active_members; ?></h3>
                            <p class="card-text">Active Members</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="card text-white bg-info h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-hand-holding-usd fa-2x mb-2"></i>
                            <h3 class="card-title">KSh <?php echo number_format($total_contributions, 2); ?></h3>
                            <p class="card-text">This Month's Giving</p>
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
                    <div class="card text-white bg-secondary h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-clipboard-check fa-2x mb-2"></i>
                            <h3 class="card-title"><?php echo $recent_attendance; ?></h3>
                            <p class="card-text">Last Service Attendance</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card shadow-sm accent-gold">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Recent Members</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-user me-1"></i>Name</th>
                                            <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                            <th><i class="fas fa-calendar me-1"></i>Join Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $recent = $conn->query("SELECT * FROM members ORDER BY created_at DESC LIMIT 5");
                                        while ($member = $recent->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                                            <td><span class="badge bg-<?php echo strtolower($member['membership_status']) == 'active' ? 'success' : 'secondary'; ?>"><?php echo $member['membership_status']; ?></span></td>
                                            <td><?php echo date('M d, Y', strtotime($member['created_at'])); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
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
                                <div class="d-flex align-items-center mb-3 p-2 bg-light rounded">
                                    <div class="text-center me-3" style="min-width: 60px;">
                                        <div class="bg-primary text-white rounded p-2">
                                            <div class="fw-bold"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                                            <small><?php echo date('M', strtotime($event['event_date'])); ?></small>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($event['location']); ?></small>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
