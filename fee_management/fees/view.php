<?php
session_start();
require_once '../config/db_connect.php';

// Check if user is not logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Get filter values
$filter_stream = isset($_GET['stream']) ? $_GET['stream'] : '';
$filter_branch = isset($_GET['branch']) ? $_GET['branch'] : '';
$filter_year = isset($_GET['year']) ? $_GET['year'] : '';

// Build the query
$query = "
    SELECT f.*, s.name, s.admission_no 
    FROM fees f 
    JOIN students s ON f.student_id = s.id 
    WHERE 1=1
";
$params = [];
$types = "";

if (!empty($filter_stream)) {
    $query .= " AND f.stream = ?";
    $params[] = $filter_stream;
    $types .= "s";
}
if (!empty($filter_branch)) {
    $query .= " AND f.branch = ?";
    $params[] = $filter_branch;
    $types .= "s";
}
if (!empty($filter_year)) {
    $query .= " AND f.year = ?";
    $params[] = $filter_year;
    $types .= "s";
}

$query .= " ORDER BY f.payment_date DESC";

// Execute query with filters
if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

// Calculate total fees
$total_fees = 0;
$fees = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $total_fees += $row['amount_paid'];
        $fees[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Fee Payments - JNTUACEA Fee Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-header">
        <div class="logo-section">
            <img src="../assets/images/jntua-logo.png" alt="JNTUACEA Logo">
            <div>
                <h1>JNTUACEA</h1>
                <p>Fee Management System</p>
            </div>
        </div>
        <div class="user-nav">
            <a href="../dashboard.php" class="action-button">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="add.php" class="action-button">
                <i class="fas fa-plus"></i> Add Fee
            </a>
            <a href="../includes/logout.php" class="action-button">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="content-wrapper">
            <div class="filter-section">
                <h2>Filter Fee Payments</h2>
                <form method="GET" class="filter-form">
                    <div class="filter-group">
                        <label for="stream">Stream:</label>
                        <select name="stream" id="stream">
                            <option value="">All Streams</option>
                            <option value="B.Tech" <?php echo $filter_stream === 'B.Tech' ? 'selected' : ''; ?>>B.Tech</option>
                            <option value="M.Tech" <?php echo $filter_stream === 'M.Tech' ? 'selected' : ''; ?>>M.Tech</option>
                            <option value="MCA" <?php echo $filter_stream === 'MCA' ? 'selected' : ''; ?>>MCA</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="branch">Branch:</label>
                        <select name="branch" id="branch">
                            <option value="">All Branches</option>
                            <option value="CSE" <?php echo $filter_branch === 'CSE' ? 'selected' : ''; ?>>Computer Science & Engineering</option>
                            <option value="ECE" <?php echo $filter_branch === 'ECE' ? 'selected' : ''; ?>>Electronics & Communication Engineering</option>
                            <option value="EEE" <?php echo $filter_branch === 'EEE' ? 'selected' : ''; ?>>Electrical & Electronics Engineering</option>
                            <option value="MECH" <?php echo $filter_branch === 'MECH' ? 'selected' : ''; ?>>Mechanical Engineering</option>
                            <option value="CIVIL" <?php echo $filter_branch === 'CIVIL' ? 'selected' : ''; ?>>Civil Engineering</option>
                            <option value="CHEM" <?php echo $filter_branch === 'CHEM' ? 'selected' : ''; ?>>Chemical Engineering</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="year">Year:</label>
                        <select name="year" id="year">
                            <option value="">All Years</option>
                            <option value="1" <?php echo $filter_year === '1' ? 'selected' : ''; ?>>1st Year</option>
                            <option value="2" <?php echo $filter_year === '2' ? 'selected' : ''; ?>>2nd Year</option>
                            <option value="3" <?php echo $filter_year === '3' ? 'selected' : ''; ?>>3rd Year</option>
                            <option value="4" <?php echo $filter_year === '4' ? 'selected' : ''; ?>>4th Year</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <button type="submit" class="action-button">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="view.php" class="action-button cancel-button">
                            <i class="fas fa-sync"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="fees-section">
                <div class="fees-header">
                    <h3>Fee Payments</h3>
                    <div class="total-fees">
                        Total Collected: <span>₹<?php echo number_format($total_fees, 2); ?></span>
                    </div>
                </div>

                <?php if (!empty($fees)): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Admission No.</th>
                                <th>Reference No.</th>
                                <th>Amount Paid</th>
                                <th>Payment Date</th>
                                <th>Year</th>
                                <th>Stream</th>
                                <th>Branch</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fees as $fee): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($fee['name']); ?></td>
                                    <td><?php echo htmlspecialchars($fee['admission_no']); ?></td>
                                    <td><?php echo htmlspecialchars($fee['ref_no']); ?></td>
                                    <td>₹<?php echo number_format($fee['amount_paid'], 2); ?></td>
                                    <td><?php echo date('d M Y', strtotime($fee['payment_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($fee['year']); ?> Year</td>
                                    <td><?php echo htmlspecialchars($fee['stream']); ?></td>
                                    <td><?php echo htmlspecialchars($fee['branch']); ?></td>
                                    <td class="action-buttons">
                                        <a href="../students/view_details.php?id=<?php echo $fee['student_id']; ?>" class="table-action-btn view-btn" title="View Student">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?php echo $fee['id']; ?>" class="table-action-btn edit-btn" title="Edit Fee">
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