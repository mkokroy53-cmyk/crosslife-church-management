<div class="sidebar">
    <div class="sidebar-header">
        <h2>⛪ Church Pro</h2>
    </div>
    
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item active">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="members.php" class="nav-item">
            <i class="fas fa-users"></i>
            <span>Members</span>
        </a>
        
        <a href="attendance.php" class="nav-item">
            <i class="fas fa-clipboard-check"></i>
            <span>Attendance</span>
        </a>
        
        <a href="contributions.php" class="nav-item">
            <i class="fas fa-hand-holding-usd"></i>
            <span>Contributions</span>
        </a>
        
        <a href="events.php" class="nav-item">
            <i class="fas fa-calendar-alt"></i>
            <span>Events</span>
        </a>
        
        <a href="groups.php" class="nav-item">
            <i class="fas fa-user-friends"></i>
            <span>Groups</span>
        </a>
        
        <a href="prayers.php" class="nav-item">
            <i class="fas fa-praying-hands"></i>
            <span>Prayer Requests</span>
        </a>
        
        <a href="expenses.php" class="nav-item">
            <i class="fas fa-receipt"></i>
            <span>Expenses</span>
        </a>
        
        <a href="reports.php" class="nav-item">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
        
        <?php if (hasRole('admin')): ?>
        <a href="users.php" class="nav-item">
            <i class="fas fa-user-shield"></i>
            <span>Users</span>
        </a>
        <?php endif; ?>
    </nav>
</div>

<script>
// Highlight active menu item
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('href') === currentPage) {
            item.classList.add('active');
        }
    });
});
</script>
