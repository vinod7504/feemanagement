<?php
session_start();
require_once '../config/db_connect.php';

// Check if user is not logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Check if student ID is provided
if(!isset($_GET['id'])) {
    $_SESSION['error'] = "Student ID not provided";
    header("Location: view.php");
    exit();
}

$student_id = (int)$_GET['id'];

// Fetch student details
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Student not found";
    header("Location: view.php");
    exit();
}

$student = $result->fetch_assoc();

// Fetch fee history
$stmt = $conn->prepare("SELECT * FROM fees WHERE student_id = ? ORDER BY payment_date DESC");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$fees_result = $stmt->get_result();

// Calculate total fees paid
$total_fees = 0;
$fees_history = [];
while ($fee = $fees_result->fetch_assoc()) {
    $total_fees += $fee['amount_paid'];
    $fees_history[] = $fee;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details - JNTUACEA Fee Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-header">
        <div class="logo-section">
            <img src="https://www.jntuacea.ac.in/images/jntuaceatp.png" alt="JNTUACEA Logo">
            <div>
                <h1>JNTUACEA</h1>
                <p>Fee Management System</p>
            </div>
        </div>
        <div class="user-nav">
            <a href="../dashboard.php" class="action-button">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="view.php" class="action-button">
                <i class="fas fa-list"></i> All Students
            </a>
            <a href="../includes/logout.php" class="action-button">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="student-profile">
            <div class="profile-header">
                <div class="profile-image">
                    <?php if (!empty($student['image_path'])): ?>
                        <img src="../<?php echo htmlspecialchars($student['image_path']); ?>" alt="Student Photo">
                    <?php else: ?>
                        <div class="no-image">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($student['name']); ?></h2>
                    <p class="admission-no">
                        <i class="fas fa-id-card"></i>
                        <?php echo htmlspecialchars($student['admission_no']); ?>
                    </p>
                    <div class="quick-info">
                        <div class="info-item">
                            <span class="label">Year:</span>
                            <span class="value"><?php echo htmlspecialchars($student['year']); ?> Year</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Stream:</span>
                            <span class="value"><?php echo htmlspecialchars($student['stream']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Branch:</span>
                            <span class="value"><?php echo htmlspecialchars($student['branch']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Phone:</span>
                            <span class="value">
                                <i class="fas fa-phone"></i>
                                <?php echo htmlspecialchars($student['phone']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="profile-actions">
                    <a href="../fees/add.php?student_id=<?php echo $student_id; ?>" class="action-button">
                        <i class="fas fa-money-bill-wave"></i> Add New Fee
                    </a>
                </div>
            </div>

            <div class="fees-section">
                <div class="fees-header">
                    <h3>Fee History</h3>
                    <div class="total-fees">
                        Total Paid: <span>₹<?php echo number_format($total_fees, 2); ?></span>
                    </div>
                </div>

                <?php if (!empty($fees_history)): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Reference No.</th>
                                <th>Amount Paid</th>
                                <th>Payment Date</th>
                                <th>Year</th>
                                <th>Stream</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fees_history as $fee): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($fee['ref_no']); ?></td>
                                    <td>₹<?php echo number_format($fee['amount_paid'], 2); ?></td>
                                    <td><?php echo date('d M Y', strtotime($fee['payment_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($fee['year']); ?> Year</td>
                                    <td><?php echo htmlspecialchars($fee['stream']); ?></td>
                                    <td class="action-buttons">
                                        <a href="../fees/edit.php?id=<?php echo $fee['id']; ?>" class="table-action-btn edit-btn" title="Edit Fee">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-records">No fee records found</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html> 