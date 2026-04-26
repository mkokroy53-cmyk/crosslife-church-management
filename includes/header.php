<div class="header">
    <div class="header-left">
        <button class="menu-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <div class="header-right">
        <div class="user-info">
            <span class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            <span class="user-role"><?php echo ucfirst($_SESSION['role']); ?></span>
        </div>
        <a href="logout.php" class="logout-btn" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</div>

<script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('active');
}
</script>
