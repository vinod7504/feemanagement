<?php
session_start();
require_once '../config/db_connect.php';

// Check if user is not logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Check if fee ID is provided
if(!isset($_GET['id'])) {
    $_SESSION['error'] = "Fee ID not provided";
    header("Location: ../dashboard.php");
    exit();
}

$fee_id = (int)$_GET['id'];

// Fetch fee details
$stmt = $conn->prepare("
    SELECT f.*, s.name, s.admission_no 
    FROM fees f 
    JOIN students s ON f.student_id = s.id 
    WHERE f.id = ?
");
$stmt->bind_param("i", $fee_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Fee record not found";
    header("Location: ../dashboard.php");
    exit();
}

$fee = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Fee Payment - JNTUACEA Fee Management</title>
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
            <a href="../includes/logout.php" class="action-button">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="form-container">
            <h2>Edit Fee Payment</h2>
            
            <?php
            if(isset($_SESSION['success'])) {
                echo '<div class="success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            }
            if(isset($_SESSION['error'])) {
                echo '<div class="error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <div class="student-info">
                <p><strong>Student Name:</strong> <?php echo htmlspecialchars($fee['name']); ?></p>
                <p><strong>Admission No:</strong> <?php echo htmlspecialchars($fee['admission_no']); ?></p>
            </div>

            <form action="process_edit_fee.php" method="POST" id="feeForm">
                <input type="hidden" name="fee_id" value="<?php echo $fee_id; ?>">
                <input type="hidden" name="student_id" value="<?php echo $fee['student_id']; ?>">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="ref_no">Reference Number:</label>
                        <input type="text" id="ref_no" name="ref_no" class="form-control" value="<?php echo htmlspecialchars($fee['ref_no']); ?>" required>
                        <small>Unique payment reference number</small>
                    </div>

                    <div class="form-group">
                        <label for="amount_paid">Amount Paid:</label>
                        <input type="number" id="amount_paid" name="amount_paid" class="form-control" min="1" step="0.01" value="<?php echo htmlspecialchars($fee['amount_paid']); ?>" required>
                        <small>Amount in Indian Rupees (₹)</small>
                    </div>

                    <div class="form-group">
                        <label for="year">Year:</label>
                        <select id="year" name="year" class="form-control" required>
                            <option value="">Select Year</option>
                            <option value="1" <?php echo $fee['year'] == '1' ? 'selected' : ''; ?>>1st Year</option>
                            <option value="2" <?php echo $fee['year'] == '2' ? 'selected' : ''; ?>>2nd Year</option>
                            <option value="3" <?php echo $fee['year'] == '3' ? 'selected' : ''; ?>>3rd Year</option>
                            <option value="4" <?php echo $fee['year'] == '4' ? 'selected' : ''; ?>>4th Year</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="stream">Stream:</label>
                        <select id="stream" name="stream" class="form-control" required>
                            <option value="">Select Stream</option>
                            <option value="B.Tech" <?php echo $fee['stream'] == 'B.Tech' ? 'selected' : ''; ?>>B.Tech</option>
                            <option value="M.Tech" <?php echo $fee['stream'] == 'M.Tech' ? 'selected' : ''; ?>>M.Tech</option>
                            <option value="MCA" <?php echo $fee['stream'] == 'MCA' ? 'selected' : ''; ?>>MCA</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="branch">Branch:</label>
                        <select id="branch" name="branch" class="form-control" required>
                            <option value="">Select Branch</option>
                            <option value="CSE" <?php echo $fee['branch'] == 'CSE' ? 'selected' : ''; ?>>Computer Science & Engineering</option>
                            <option value="MECH" <?php echo $fee['branch'] == 'MECH' ? 'selected' : ''; ?>>Mechanical Engineering</option>
                            <option value="EEE" <?php echo $fee['branch'] == 'EEE' ? 'selected' : ''; ?>>Electrical & Electronics Engineering</option>
                            <option value="ECE" <?php echo $fee['branch'] == 'ECE' ? 'selected' : ''; ?>>Electronics & Communication Engineering</option>
                            <option value="CIVIL" <?php echo $fee['branch'] == 'CIVIL' ? 'selected' : ''; ?>>Civil Engineering</option>
                            <option value="CHEM" <?php echo $fee['branch'] == 'CHEM' ? 'selected' : ''; ?>>Chemical Engineering</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="submit" class="action-button">
                        <i class="fas fa-save"></i> Update Payment
                    </button>
                    <a href="../students/view_details.php?id=<?php echo $fee['student_id']; ?>" class="action-button cancel-button">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 