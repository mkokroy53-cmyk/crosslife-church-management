<?php
require_once 'config.php';

if (!isset($_SESSION['member_id'])) {
    redirect('member_login.php');
}

// Financial Summary (General - not detailed)
$total_income = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM contributions")->fetch_assoc()['total'];
$total_expenses = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM expenses")->fetch_assoc()['total'];
$balance = $total_income - $total_expenses;

// Monthly Income
$monthly_income = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM contributions WHERE MONTH(contribution_date) = MONTH(CURRENT_DATE()) AND YEAR(contribution_date) = YEAR(CURRENT_DATE())")->fetch_assoc()['total'];
$monthly_expenses = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM expenses WHERE MONTH(expense_date) = MONTH(CURRENT_DATE()) AND YEAR(expense_date) = YEAR(CURRENT_DATE())")->fetch_assoc()['total'];

// Expense by Category
$by_category = $conn->query("SELECT category, SUM(amount) as total FROM expenses GROUP BY category ORDER BY total DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Member Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/member_sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/member_header.php'; ?>
        
        <div class="content">
            <h1>Financial Reports</h1>
            
            <div class="stats-grid">
                <div class="stat-card green">
                    <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
                    <div class="stat-details">
                        <h3>KSh <?php echo number_format($total_income, 2); ?></h3>
                        <p>Total Income</p>
                    </div>
                </div>
                
                <div class="stat-card orange">
                    <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
                    <div class="stat-details">
                        <h3>KSh <?php echo number_format($total_expenses, 2); ?></h3>
                        <p>Total Expenses</p>
                    </div>
                </div>
                
                <div class="stat-card <?php echo $balance >= 0 ? 'blue' : 'orange'; ?>">
                    <div class="stat-icon"><i class="fas fa-balance-scale"></i></div>
                    <div class="stat-details">
                        <h3>KSh <?php echo number_format($balance, 2); ?></h3>
                        <p>Net Balance</p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="card">
                    <h2>This Month</h2>
                    <div style="padding: 20px 0;">
                        <div style="display: flex; justify-content: space-between; padding: 15px; background: #f0fff4; border-radius: 8px; margin-bottom: 10px;">
                            <span>Income</span>
                            <strong style="color: #48bb78;">+KSh <?php echo number_format($monthly_income, 2); ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px; background: #fff5f5; border-radius: 8px; margin-bottom: 10px;">
                            <span>Expenses</span>
                            <strong style="color: #f56565;">-KSh <?php echo number_format($monthly_expenses, 2); ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px; background: #f7fafc; border-radius: 8px; font-size: 18px;">
                            <span><strong>Net</strong></span>
                            <strong style="color: <?php echo ($monthly_income - $monthly_expenses) >= 0 ? '#48bb78' : '#f56565'; ?>;">
                                KSh <?php echo number_format($monthly_income - $monthly_expenses, 2); ?>
                            </strong>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <h2>Expenses by Category</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($cat = $by_category->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cat['category']); ?></td>
                                <td><strong>KSh <?php echo number_format($cat['total'], 2); ?></strong></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
