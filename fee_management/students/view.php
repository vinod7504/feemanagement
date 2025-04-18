<?php
session_start();
require_once '../config/db_connect.php';

// Check if user is not logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Get filter values if set
$stream = isset($_GET['stream']) ? $_GET['stream'] : '';
$branch = isset($_GET['branch']) ? $_GET['branch'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

// Base query
$query = "SELECT * FROM students WHERE 1=1";
$params = [];
$types = "";

// Add filters if set
if (!empty($stream)) {
    $query .= " AND stream = ?";
    $params[] = $stream;
    $types .= "s";
}
if (!empty($branch)) {
    $query .= " AND branch = ?";
    $params[] = $branch;
    $types .= "s";
}
if (!empty($year)) {
    $query .= " AND year = ?";
    $params[] = $year;
    $types .= "s";
}

$query .= " ORDER BY name";

// Prepare and execute query
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students - JNTUACEA Fee Management</title>
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
            <a href="add.php" class="action-button">
                <i class="fas fa-user-plus"></i> Add Student
            </a>
            <a href="../includes/logout.php" class="action-button">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="content-wrapper">
            <div class="filter-section">
                <h2>Filter Students</h2>
                <form id="filterForm" class="filter-form">
                    <div class="filter-group">
                        <label for="stream">Stream:</label>
                        <select name="stream" id="stream">
                            <option value="">All Streams</option>
                            <option value="B.Tech" <?php echo $stream === 'B.Tech' ? 'selected' : ''; ?>>B.Tech</option>
                            <option value="M.Tech" <?php echo $stream === 'M.Tech' ? 'selected' : ''; ?>>M.Tech</option>
                            <option value="MCA" <?php echo $stream === 'MCA' ? 'selected' : ''; ?>>MCA</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="branch">Branch:</label>
                        <select name="branch" id="branch">
                            <option value="">All Branches</option>
                            <option value="CSE" <?php echo $branch === 'CSE' ? 'selected' : ''; ?>>Computer Science & Engineering</option>
                            <option value="ECE" <?php echo $branch === 'ECE' ? 'selected' : ''; ?>>Electronics & Communication Engineering</option>
                            <option value="EEE" <?php echo $branch === 'EEE' ? 'selected' : ''; ?>>Electrical & Electronics Engineering</option>
                            <option value="MECH" <?php echo $branch === 'MECH' ? 'selected' : ''; ?>>Mechanical Engineering</option>
                            <option value="CIVIL" <?php echo $branch === 'CIVIL' ? 'selected' : ''; ?>>Civil Engineering</option>
                            <option value="CHEM" <?php echo $branch === 'CHEM' ? 'selected' : ''; ?>>Chemical Engineering</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="year">Year:</label>
                        <select name="year" id="year">
                            <option value="">All Years</option>
                            <option value="1" <?php echo $year === '1' ? 'selected' : ''; ?>>1st Year</option>
                            <option value="2" <?php echo $year === '2' ? 'selected' : ''; ?>>2nd Year</option>
                            <option value="3" <?php echo $year === '3' ? 'selected' : ''; ?>>3rd Year</option>
                            <option value="4" <?php echo $year === '4' ? 'selected' : ''; ?>>4th Year</option>
                        </select>
                    </div>

                    <button type="submit" class="action-button">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <button type="button" id="resetFilters" class="action-button cancel-button">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Admission No.</th>
                            <th>Year</th>
                            <th>Stream</th>
                            <th>Branch</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        <?php
                        if ($result->num_rows > 0) {
                            while ($student = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($student['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($student['admission_no']) . "</td>";
                                echo "<td>" . htmlspecialchars($student['year']) . " Year</td>";
                                echo "<td>" . htmlspecialchars($student['stream']) . "</td>";
                                echo "<td>" . htmlspecialchars($student['branch']) . "</td>";
                                echo "<td class='action-buttons'>";
                                echo "<a href='view_details.php?id=" . $student['id'] . "' class='table-action-btn view-btn' title='View Details'>";
                                echo "<i class='fas fa-eye'></i>";
                                echo "</a>";
                                echo "<button onclick='confirmDelete(" . $student['id'] . ")' class='table-action-btn delete-btn' title='Delete Student'>";
                                echo "<i class='fas fa-trash'></i>";
                                echo "</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='no-records'>No students found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Handle filter form submission
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const params = new URLSearchParams(formData);
            window.location.href = 'view.php?' + params.toString();
        });

        // Reset filters
        document.getElementById('resetFilters').addEventListener('click', function() {
            window.location.href = 'view.php';
        });

        // Confirm delete
        function confirmDelete(studentId) {
            if (confirm('Are you sure you want to delete this student? This action cannot be undone.')) {
                window.location.href = `delete.php?id=${studentId}`;
            }
        }
    </script>
</body>
</html> 