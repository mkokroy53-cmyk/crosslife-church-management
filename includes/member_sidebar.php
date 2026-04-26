<div class="sidebar">
    <div class="sidebar-header">
        <h2>⛪ Crosslife Church</h2>
        <p style="font-size: 11px; color: rgba(255,255,255,0.7); margin-top: 5px;">Member Portal</p>
    </div>
    
    <nav class="sidebar-nav">
        <a href="member_dashboard.php" class="nav-item active">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="member_attendance.php" class="nav-item">
            <i class="fas fa-clipboard-check"></i>
            <span>My Attendance</span>
        </a>
        
        <a href="member_events.php" class="nav-item">
            <i class="fas fa-calendar-alt"></i>
            <span>Events</span>
        </a>
        
        <a href="member_prayers.php" class="nav-item">
            <i class="fas fa-praying-hands"></i>
            <span>Prayer Requests</span>
        </a>
        
        <a href="member_reports.php" class="nav-item">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
    </nav>
</div>

<script>
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
