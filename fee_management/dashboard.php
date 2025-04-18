<?php
require_once 'config/init.php';

// Check if user is not logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Get total number of students
$result = $conn->query("SELECT COUNT(*) as total_students FROM students");
$total_students = $result->fetch_assoc()['total_students'];

// Get total fees collected
$result = $conn->query("SELECT COALESCE(SUM(amount_paid), 0) as total_fees FROM fees");
$total_fees = $result->fetch_assoc()['total_fees'];

// Get recent students (last 5)
$result = $conn->query("SELECT * FROM students ORDER BY created_at DESC LIMIT 5");
$recent_students = $result->fetch_all(MYSQLI_ASSOC);

// Get recent fee payments (last 5)
$result = $conn->query("
    SELECT f.*, s.name, s.admission_no 
    FROM fees f 
    JOIN students s ON f.student_id = s.id 
    ORDER BY f.payment_date DESC 
    LIMIT 5
");
$recent_fees = $result->fetch_all(MYSQLI_ASSOC);

$page_title = 'Dashboard';
require_once 'includes/header.php';
?>

<div class="page-container">
    <!-- Stats Section -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Students</h3>
            <div class="number"><?php echo number_format($total_students); ?></div>
            <a href="students/view.php" class="btn btn-secondary">View All Students</a>
        </div>
        <div class="stat-card">
            <h3>Total Fees Collected</h3>
            <div class="number">₹<?php echo number_format($total_fees, 2); ?></div>
            <a href="fees/view.php" class="btn btn-secondary">View All Fees</a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="content-card">
        <h2 class="page-title">Quick Actions</h2>
        <div class="action-buttons">
            <a href="students/add.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Add New Student
            </a>
            <a href="fees/add.php" class="btn btn-primary">
                <i class="fas fa-money-bill-wave"></i> Add Fee Payment
            </a>
            <a href="students/view.php" class="btn btn-secondary">
                <i class="fas fa-users"></i> Manage Students
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="dashboard-grid">
        <!-- Recent Students -->
        <div class="content-card">
            <h2 class="page-title">Recent Students</h2>
            <?php if (!empty($recent_students)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Admission No.</th>
                            <th>Branch</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td><?php echo htmlspecialchars($student['admission_no']); ?></td>
                                <td><?php echo htmlspecialchars($student['branch']); ?></td>
                                <td>
                                    <a href="students/view_details.php?id=<?php echo $student['id']; ?>" 
                                       class="btn btn-secondary btn-sm">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-records">No students added yet</p>
            <?php endif; ?>
        </div>

        <!-- Recent Fee Payments -->
        <div class="content-card">
            <h2 class="page-title">Recent Fee Payments</h2>
            <?php if (!empty($recent_fees)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_fees as $fee): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fee['name']); ?></td>
                                <td>₹<?php echo number_format($fee['amount_paid'], 2); ?></td>
                                <td><?php echo date('d M Y', strtotime($fee['payment_date'])); ?></td>
                                <td><?php echo htmlspecialchars($fee['ref_no']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-records">No fee payments recorded yet</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 