<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, event_time, location, event_type, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $_POST['title'], $_POST['description'], $_POST['event_date'], $_POST['event_time'], $_POST['location'], $_POST['event_type'], $_SESSION['user_id']);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Event created successfully!</div>';
        }
    } elseif ($_POST['action'] === 'edit') {
        $stmt = $conn->prepare("UPDATE events SET title=?, description=?, event_date=?, event_time=?, location=?, event_type=? WHERE id=?");
        $stmt->bind_param("ssssssi", $_POST['title'], $_POST['description'], $_POST['event_date'], $_POST['event_time'], $_POST['location'], $_POST['event_type'], $_POST['id']);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Event updated successfully!</div>';
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Event deleted successfully!</div>';
    }
}

// Get events
$events = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Church Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <h1>Events Management</h1>
            
            <?php echo $message; ?>
            
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>All Events</h2>
                    <button onclick="openModal()" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Event
                    </button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Actions</th>
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
                            <td>
                                <button onclick='editEvent(<?php echo json_encode($event); ?>)' class="action-btn edit">Edit</button>
                                <a href="?delete=<?php echo $event['id']; ?>" onclick="return confirm('Are you sure?')" class="action-btn delete">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Create Event</h2>
                <button onclick="closeModal()" class="close-modal">&times;</button>
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="eventId">
                
                <div class="form-group">
                    <label>Event Title *</label>
                    <input type="text" name="title" id="title" required>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Event Date *</label>
                        <input type="date" name="event_date" id="event_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Event Time</label>
                        <input type="time" name="event_time" id="event_time">
                    </div>
                    
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" id="location">
                    </div>
                    
                    <div class="form-group">
                        <label>Event Type</label>
                        <input type="text" name="event_type" id="event_type" placeholder="e.g., Conference, Workshop">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="description"></textarea>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Save Event</button>
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openModal() {
            document.getElementById('eventModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Create Event';
            document.getElementById('formAction').value = 'add';
            document.querySelector('form').reset();
        }
        
        function closeModal() {
            document.getElementById('eventModal').classList.remove('active');
        }
        
        function editEvent(event) {
            document.getElementById('eventModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Edit Event';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('eventId').value = event.id;
            document.getElementById('title').value = event.title;
            document.getElementById('event_date').value = event.event_date;
            document.getElementById('event_time').value = event.event_time || '';
            document.getElementById('location').value = event.location || '';
            document.getElementById('event_type').value = event.event_type || '';
            document.getElementById('description').value = event.description || '';
        }
    </script>
</body>
</html>
