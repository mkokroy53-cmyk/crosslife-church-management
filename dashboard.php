<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('index.php');
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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <h1>Dashboard</h1>
            
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-details">
                        <h3><?php echo $total_members; ?></h3>
                        <p>Total Members</p>
                    </div>
                </div>
                
                <div class="stat-card green">
                    <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                    <div class="stat-details">
                        <h3><?php echo $active_members; ?></h3>
                        <p>Active Members</p>
                    </div>
                </div>
                
                <div class="stat-card purple">
                    <div class="stat-icon"><i class="fas fa-hand-holding-usd"></i></div>
                    <div class="stat-details">
                        <h3>$<?php echo number_format($total_contributions, 2); ?></h3>
                        <p>This Month's Giving</p>
                    </div>
                </div>
                
                <div class="stat-card orange">
                    <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="stat-details">
                        <h3><?php echo $upcoming_events; ?></h3>
                        <p>Upcoming Events</p>
                    </div>
                </div>
                
                <div class="stat-card teal">
                    <div class="stat-icon"><i class="fas fa-clipboard-check"></i></div>
                    <div class="stat-details">
                        <h3><?php echo $recent_attendance; ?></h3>
                        <p>Last Service Attendance</p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="card">
                    <h2>Recent Members</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Join Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent = $conn->query("SELECT * FROM members ORDER BY created_at DESC LIMIT 5");
                            while ($member = $recent->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                                <td><span class="badge <?php echo strtolower($member['membership_status']); ?>"><?php echo $member['membership_status']; ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($member['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="card">
                    <h2>Upcoming Events</h2>
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
</body>
</html>
