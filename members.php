<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $stmt = $conn->prepare("INSERT INTO members (first_name, last_name, email, phone, address, date_of_birth, gender, marital_status, membership_status, baptism_date, join_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssss", $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['date_of_birth'], $_POST['gender'], $_POST['marital_status'], $_POST['membership_status'], $_POST['baptism_date'], $_POST['join_date']);
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Member added successfully!</div>';
            }
        } elseif ($_POST['action'] === 'edit') {
            $stmt = $conn->prepare("UPDATE members SET first_name=?, last_name=?, email=?, phone=?, address=?, date_of_birth=?, gender=?, marital_status=?, membership_status=?, baptism_date=?, join_date=? WHERE id=?");
            $stmt->bind_param("sssssssssssi", $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['date_of_birth'], $_POST['gender'], $_POST['marital_status'], $_POST['membership_status'], $_POST['baptism_date'], $_POST['join_date'], $_POST['id']);
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Member updated successfully!</div>';
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM members WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Member deleted successfully!</div>';
    }
}

// Get all members
$search = $_GET['search'] ?? '';
$query = "SELECT * FROM members WHERE CONCAT(first_name, ' ', last_name, ' ', email, ' ', phone) LIKE ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$searchTerm = "%$search%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$members = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members - Church Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <h1>Members Management</h1>
            
            <?php echo $message; ?>
            
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <form method="GET" class="search-bar" style="margin: 0; flex: 1; max-width: 400px;">
                        <input type="text" name="search" placeholder="Search members..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                    <button onclick="openModal()" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Member
                    </button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Join Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($member = $members->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($member['email']); ?></td>
                            <td><?php echo htmlspecialchars($member['phone']); ?></td>
                            <td><span class="badge <?php echo strtolower($member['membership_status']); ?>"><?php echo $member['membership_status']; ?></span></td>
                            <td><?php echo $member['join_date'] ? date('M d, Y', strtotime($member['join_date'])) : 'N/A'; ?></td>
                            <td>
                                <button onclick='editMember(<?php echo json_encode($member); ?>)' class="action-btn edit">Edit</button>
                                <a href="?delete=<?php echo $member['id']; ?>" onclick="return confirm('Are you sure?')" class="action-btn delete">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div id="memberModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add Member</h2>
                <button onclick="closeModal()" class="close-modal">&times;</button>
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="memberId">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" name="first_name" id="first_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Last Name *</label>
                        <input type="text" name="last_name" id="last_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="email">
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" id="phone">
                    </div>
                    
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth">
                    </div>
                    
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" id="gender">
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Marital Status</label>
                        <select name="marital_status" id="marital_status">
                            <option value="">Select</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Divorced">Divorced</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Membership Status</label>
                        <select name="membership_status" id="membership_status">
                            <option value="Visitor">Visitor</option>
                            <option value="New Convert">New Convert</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Baptism Date</label>
                        <input type="date" name="baptism_date" id="baptism_date">
                    </div>
                    
                    <div class="form-group">
                        <label>Join Date</label>
                        <input type="date" name="join_date" id="join_date">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" id="address"></textarea>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Save Member</button>
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openModal() {
            document.getElementById('memberModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Add Member';
            document.getElementById('formAction').value = 'add';
            document.querySelector('form').reset();
        }
        
        function closeModal() {
            document.getElementById('memberModal').classList.remove('active');
        }
        
        function editMember(member) {
            document.getElementById('memberModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Edit Member';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('memberId').value = member.id;
            document.getElementById('first_name').value = member.first_name;
            document.getElementById('last_name').value = member.last_name;
            document.getElementById('email').value = member.email || '';
            document.getElementById('phone').value = member.phone || '';
            document.getElementById('address').value = member.address || '';
            document.getElementById('date_of_birth').value = member.date_of_birth || '';
            document.getElementById('gender').value = member.gender || '';
            document.getElementById('marital_status').value = member.marital_status || '';
            document.getElementById('membership_status').value = member.membership_status;
            document.getElementById('baptism_date').value = member.baptism_date || '';
            document.getElementById('join_date').value = member.join_date || '';
        }
    </script>
</body>
</html>
