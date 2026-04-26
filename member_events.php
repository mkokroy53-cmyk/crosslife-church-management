<?php
require_once 'config.php';

if (!isset($_SESSION['member_id'])) {
    redirect('member_login.php');
}

// Get all upcoming events
$events = $conn->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Member Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/member_sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/member_header.php'; ?>
        
        <div class="content">
            <h1>Upcoming Events</h1>
            
            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($event = $events->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($event['title']); ?></strong></td>
                            <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                            <td><?php echo $event['event_time'] ? date('h:i A', strtotime($event['event_time'])) : 'N/A'; ?></td>
                            <td><?php echo htmlspecialchars($event['location']); ?></td>
                            <td><?php echo htmlspecialchars($event['event_type']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
