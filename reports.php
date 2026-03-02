<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('index.php');
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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content">
            <h1>Reports & Analytics</h1>
            
            <div class="stats-grid">
                <div class="stat-card green">
                    <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
                    <div class="stat-details">
                        <h3>$<?php echo number_format($total_income, 2); ?></h3>
                        <p>Total Income</p>
                    </div>
                </div>
                
                <div class="stat-card orange">
                    <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
                    <div class="stat-details">
                        <h3>$<?php echo number_format($total_expenses, 2); ?></h3>
                        <p>Total Expenses</p>
                    </div>
                </div>
                
                <div class="stat-card <?php echo $balance >= 0 ? 'blue' : 'orange'; ?>">
                    <div class="stat-icon"><i class="fas fa-balance-scale"></i></div>
                    <div class="stat-details">
                        <h3>$<?php echo number_format($balance, 2); ?></h3>
                        <p>Net Balance</p>
                    </div>
                </div>
                
                <div class="stat-card purple">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-details">
                        <h3><?php echo round($avg_attendance ?? 0); ?></h3>
                        <p>Avg. Attendance</p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="card">
                    <h2>This Month</h2>
                    <div style="padding: 20px 0;">
                        <div style="display: flex; justify-content: space-between; padding: 15px; background: #f0fff4; border-radius: 8px; margin-bottom: 10px;">
                            <span>Income</span>
                            <strong style="color: #48bb78;">+$<?php echo number_format($monthly_income, 2); ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px; background: #fff5f5; border-radius: 8px; margin-bottom: 10px;">
                            <span>Expenses</span>
                            <strong style="color: #f56565;">-$<?php echo number_format($monthly_expenses, 2); ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px; background: #f7fafc; border-radius: 8px; font-size: 18px;">
                            <span><strong>Net</strong></span>
                            <strong style="color: <?php echo ($monthly_income - $monthly_expenses) >= 0 ? '#48bb78' : '#f56565'; ?>;">
                                $<?php echo number_format($monthly_income - $monthly_expenses, 2); ?>
                            </strong>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <h2>Contributions by Type</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($type = $by_type->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $type['contribution_type']; ?></td>
                                <td><strong>$<?php echo number_format($type['total'], 2); ?></strong></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card" style="margin-top: 20px;">
                <h2>Monthly Income Trend (Last 6 Months)</h2>
                <div style="padding: 20px;">
                    <?php while ($trend = $monthly_trends->fetch_assoc()): 
                        $percentage = ($total_income > 0) ? ($trend['total'] / $total_income * 100) : 0;
                    ?>
                    <div style="margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span><?php echo date('F Y', strtotime($trend['month'] . '-01')); ?></span>
                            <strong>$<?php echo number_format($trend['total'], 2); ?></strong>
                        </div>
                        <div style="background: #e2e8f0; height: 10px; border-radius: 5px; overflow: hidden;">
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 100%; width: <?php echo min($percentage, 100); ?>%;"></div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
