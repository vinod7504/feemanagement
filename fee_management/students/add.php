<?php
session_start();
require_once '../config/db_connect.php';

// Check if user is not logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - JNTUACEA Fee Management</title>
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
            <h2>Add New Student</h2>
            
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

            <form action="process_add_student.php" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="admission_no">Admission Number:</label>
                        <input type="text" id="admission_no" name="admission_no" required>
                    </div>

                    <div class="form-group">
                        <label for="year">Year:</label>
                        <select id="year" name="year" required>
                            <option value="">Select Year</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="stream">Stream:</label>
                        <select id="stream" name="stream" required>
                            <option value="">Select Stream</option>
                            <option value="B.Tech">B.Tech</option>
                            <option value="M.Tech">M.Tech</option>
                            <option value="MCA">MCA</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="branch">Branch:</label>
                        <select id="branch" name="branch" required>
                            <option value="">Select Branch</option>
                            <option value="CSE">Computer Science & Engineering</option>
                            <option value="MECH">Mechanical Engineering</option>
                            <option value="EEE">Electrical & Electronics Engineering</option>
                            <option value="ECE">Electronics & Communication Engineering</option>
                            <option value="CIVIL">Civil Engineering</option>
                            <option value="CHEM">Chemical Engineering</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number" required>
                    </div>

                    <div class="form-group image-upload">
                        <label for="image">Student Photo <span class="optional-tag">(Optional)</span></label>
                        <div class="image-preview default-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <input type="file" id="image" name="image" accept="image/*">
                        <small class="upload-info">Allowed formats: JPG, JPEG, PNG. Max size: 2MB</small>
                        <small class="default-avatar-info">A default avatar will be used if no photo is uploaded</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="submit" class="action-button">
                        <i class="fas fa-user-plus"></i> Add Student
                    </button>
                    <a href="../dashboard.php" class="action-button cancel-button">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Preview image before upload
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.querySelector('.image-preview');
            
            if (file) {
                if (file.size > 2 * 1024 * 1024) { // 2MB
                    alert('File size should not exceed 2MB');
                    this.value = '';
                    preview.innerHTML = '<i class="fas fa-user"></i>';
                    preview.classList.add('default-avatar');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    preview.classList.remove('default-avatar');
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '<i class="fas fa-user"></i>';
                preview.classList.add('default-avatar');
            }
        });
    </script>
</body>
</html> 