<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('staff_login.php');
}

// Financial Summary
$total_income = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM contributions")->fetch_assoc()['total'];
$total_expenses = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM expenses")->fetch_assoc()['total'];
$balance = $total_income - $total_expenses;

// Monthly Income
$monthly_income = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM contributions WHERE MONTH(contribution_date) = MONTH(CURRENT_DATE()) AND YEAR(contribution_date) = YEAR(CURRENT_DATE())")->fetch_assoc()['total'];
$monthly_expenses = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM expenses WHERE MONTH(expense_date) = MONTH(CURRENT_DATE()) AND YEAR(expense_date) = YEAR(CURRENT_DATE())")->fetch_assoc()['total'];

// Contribution by Type
$by_type = $conn->query("SELECT contribution_type, SUM(amount) as total FROM contributions GROUP BY contribution_type ORDER BY total DESC");

// Monthly Trends (Last 6 months)
$monthly_trends = $conn->query("SELECT DATE_FORMAT(contribution_date, '%Y-%m') as month, SUM(amount) as total FROM contributions WHERE contribution_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH) GROUP BY month ORDER BY month");

// Attendance Stats
$avg_attendance = $conn->query("SELECT AVG(attendance_count) as avg FROM (SELECT service_date, COUNT(*) as attendance_count FROM attendance GROUP BY service_date) as subquery")->fetch_assoc()['avg'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Church Management System</title>
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
                <h1 class="h2 mb-0"><i class="fas fa-chart-bar me-2"></i>Reports & Analytics</h1>
                <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print me-1"></i>Print Report</button>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-success h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-up fa-2x mb-2"></i>
                            <h3 class="card-title">KSh <?php echo number_format($total_income, 2); ?></h3>
                            <p class="card-text">Total Income</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-warning h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-down fa-2x mb-2"></i>
                            <h3 class="card-title">KSh <?php echo number_format($total_expenses, 2); ?></h3>
                            <p class="card-text">Total Expenses</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white <?php echo $balance >= 0 ? 'bg-primary' : 'bg-danger'; ?> h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-balance-scale fa-2x mb-2"></i>
                            <h3 class="card-title">KSh <?php echo number_format($balance, 2); ?></h3>
                            <p class="card-text">Net Balance</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-info h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-check fa-2x mb-2"></i>
                            <h3 class="card-title"><?php echo round($avg_attendance ?? 0); ?></h3>
                            <p class="card-text">Avg. Attendance</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm accent-gold">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-calendar-month me-2"></i>This Month</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-success bg-opacity-10 rounded">
                                <span>Income</span>
                                <strong class="text-success">+KSh <?php echo number_format($monthly_income, 2); ?></strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-danger bg-opacity-10 rounded">
                                <span>Expenses</span>
                                <strong class="text-danger">-KSh <?php echo number_format($monthly_expenses, 2); ?></strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-3 rounded fs-5" style="background: linear-gradient(135deg, #374151 0%, #1f2937 100%); color: #f8fafc;">
                                <span><strong>Net</strong></span>
                                <strong class="<?php echo ($monthly_income - $monthly_expenses) >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    KSh <?php echo number_format($monthly_income - $monthly_expenses, 2); ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card shadow-sm accent-gold">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Contributions by Type</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-tag me-1"></i>Type</th>
                                            <th><i class="fas fa-money-bill me-1"></i>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($type = $by_type->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($type['contribution_type']); ?></td>
                                            <td><strong>KSh <?php echo number_format($type['total'], 2); ?></strong></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm accent-gold">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Income Trend (Last 6 Months)</h5>
                </div>
                <div class="card-body">
                    <?php while ($trend = $monthly_trends->fetch_assoc()): 
                        $percentage = ($total_income > 0) ? ($trend['total'] / $total_income * 100) : 0;
                    ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span><?php echo date('F Y', strtotime($trend['month'] . '-01')); ?></span>
                            <strong>KSh <?php echo number_format($trend['total'], 2); ?></strong>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo min($percentage, 100); ?>%"></div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
