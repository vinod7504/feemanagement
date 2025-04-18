<?php
session_start();
require_once '../config/db_connect.php';

// Check if user is not logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Get filter values
$filter_stream = isset($_GET['filter_stream']) ? $_GET['filter_stream'] : '';
$filter_branch = isset($_GET['filter_branch']) ? $_GET['filter_branch'] : '';
$filter_year = isset($_GET['filter_year']) ? $_GET['filter_year'] : '';

// Fetch all students for the dropdown with filters
$students = [];
$query = "SELECT id, name, admission_no, branch, year, stream FROM students WHERE 1=1";
$params = [];
$types = "";

if (!empty($filter_stream)) {
    $query .= " AND stream = ?";
    $params[] = $filter_stream;
    $types .= "s";
}
if (!empty($filter_branch)) {
    $query .= " AND branch = ?";
    $params[] = $filter_branch;
    $types .= "s";
}
if (!empty($filter_year)) {
    $query .= " AND year = ?";
    $params[] = $filter_year;
    $types .= "s";
}

$query .= " ORDER BY name";

if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Fee Payment - JNTUACEA Fee Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Add Select2 for better dropdown experience -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
            <h2>Add Fee Payment</h2>
            
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

            <!-- Student Filter Form -->
            <div class="filter-section">
                <h3>Filter Students</h3>
                <form id="filterForm" method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="filter_stream">Stream:</label>
                        <select id="filter_stream" name="filter_stream" class="form-control">
                            <option value="">All Streams</option>
                            <option value="B.Tech" <?php echo $filter_stream === 'B.Tech' ? 'selected' : ''; ?>>B.Tech</option>
                            <option value="M.Tech" <?php echo $filter_stream === 'M.Tech' ? 'selected' : ''; ?>>M.Tech</option>
                            <option value="MCA" <?php echo $filter_stream === 'MCA' ? 'selected' : ''; ?>>MCA</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filter_branch">Branch:</label>
                        <select id="filter_branch" name="filter_branch" class="form-control">
                            <option value="">All Branches</option>
                            <option value="CSE" <?php echo $filter_branch === 'CSE' ? 'selected' : ''; ?>>Computer Science & Engineering</option>
                            <option value="MECH" <?php echo $filter_branch === 'MECH' ? 'selected' : ''; ?>>Mechanical Engineering</option>
                            <option value="EEE" <?php echo $filter_branch === 'EEE' ? 'selected' : ''; ?>>Electrical & Electronics Engineering</option>
                            <option value="ECE" <?php echo $filter_branch === 'ECE' ? 'selected' : ''; ?>>Electronics & Communication Engineering</option>
                            <option value="CIVIL" <?php echo $filter_branch === 'CIVIL' ? 'selected' : ''; ?>>Civil Engineering</option>
                            <option value="CHEM" <?php echo $filter_branch === 'CHEM' ? 'selected' : ''; ?>>Chemical Engineering</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filter_year">Year:</label>
                        <select id="filter_year" name="filter_year" class="form-control">
                            <option value="">All Years</option>
                            <option value="1" <?php echo $filter_year === '1' ? 'selected' : ''; ?>>1st Year</option>
                            <option value="2" <?php echo $filter_year === '2' ? 'selected' : ''; ?>>2nd Year</option>
                            <option value="3" <?php echo $filter_year === '3' ? 'selected' : ''; ?>>3rd Year</option>
                            <option value="4" <?php echo $filter_year === '4' ? 'selected' : ''; ?>>4th Year</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="action-button">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="add.php" class="action-button secondary"><i class="fas fa-sync"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Fee Payment Form -->
            <form action="process_add_fee.php" method="POST" id="feeForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="student_id">Select Student:</label>
                        <select id="student_id" name="student_id" class="select2" required>
                            <option value="">Select Student</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>" 
                                    data-branch="<?php echo htmlspecialchars($student['branch']); ?>"
                                    data-year="<?php echo htmlspecialchars($student['year']); ?>"
                                    data-stream="<?php echo htmlspecialchars($student['stream']); ?>">
                                    <?php echo htmlspecialchars($student['admission_no'] . ' - ' . $student['name'] . 
                                        ' (' . $student['stream'] . ' - ' . $student['branch'] . ' - ' . $student['year'] . ' Year)'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ref_no">Reference Number:</label>
                        <input type="text" id="ref_no" name="ref_no" class="form-control" required>
                        <small>Unique payment reference number</small>
                    </div>

                    <div class="form-group">
                        <label for="amount_paid">Amount Paid:</label>
                        <input type="number" id="amount_paid" name="amount_paid" class="form-control" min="1" step="0.01" required>
                        <small>Amount in Indian Rupees (₹)</small>
                    </div>

                    <div class="form-group">
                        <label for="year">Year:</label>
                        <select id="year" name="year" class="form-control" required>
                            <option value="">Select Year</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="stream">Stream:</label>
                        <select id="stream" name="stream" class="form-control" required>
                            <option value="">Select Stream</option>
                            <option value="B.Tech">B.Tech</option>
                            <option value="M.Tech">M.Tech</option>
                            <option value="MCA">MCA</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="branch">Branch:</label>
                        <select id="branch" name="branch" class="form-control" required>
                            <option value="">Select Branch</option>
                            <option value="CSE">Computer Science & Engineering</option>
                            <option value="MECH">Mechanical Engineering</option>
                            <option value="EEE">Electrical & Electronics Engineering</option>
                            <option value="ECE">Electronics & Communication Engineering</option>
                            <option value="CIVIL">Civil Engineering</option>
                            <option value="CHEM">Chemical Engineering</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="submit" class="action-button">
                        <i class="fas fa-money-bill-wave"></i> Add Payment
                    </button>
                    <a href="../dashboard.php" class="action-button cancel-button">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for better dropdown experience
            $('.select2').select2({
                placeholder: "Search by admission number or name",
                width: '100%'
            });

            // Auto-fill branch, stream and year when student is selected
            $('#student_id').change(function() {
                const selectedOption = $(this).find('option:selected');
                const branch = selectedOption.data('branch');
                const year = selectedOption.data('year');
                const stream = selectedOption.data('stream');
                
                $('#branch').val(branch);
                $('#year').val(year);
                $('#stream').val(stream);
            });

            // Generate unique reference number
            function generateRefNo() {
                const timestamp = new Date().getTime();
                const random = Math.floor(Math.random() * 1000);
                return `FEE${timestamp}${random}`;
            }

            // Set initial reference number
            if (!$('#ref_no').val()) {
                $('#ref_no').val(generateRefNo());
            }
        });
    </script>
</body>
</html> 