<?php
session_start();
require_once '../config/db_connect.php';

// Check if user is not logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Collect form data
    $name = trim($_POST['name']);
    $admission_no = trim($_POST['admission_no']);
    $year = trim($_POST['year']);
    $stream = trim($_POST['stream']);
    $branch = trim($_POST['branch']);
    $phone = trim($_POST['phone']);
    
    // Validate required fields
    if (empty($name) || empty($admission_no) || empty($year) || empty($stream) || 
        empty($branch) || empty($phone)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        exit();
    }

    // Validate phone number
    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $_SESSION['error'] = "Please enter a valid 10-digit phone number";
        header("Location: add.php");
        exit();
    }

    // Handle file upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2MB

        // Validate file type
        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['error'] = "Only JPG, JPEG, and PNG files are allowed";
            header("Location: add.php");
            exit();
        }

        // Validate file size
        if ($file['size'] > $max_size) {
            $_SESSION['error'] = "File size should not exceed 2MB";
            header("Location: add.php");
            exit();
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $upload_path = '../uploads/' . $filename;

        // Move file to uploads directory
        if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
            $_SESSION['error'] = "Failed to upload image";
            header("Location: add.php");
            exit();
        }

        $image_path = 'uploads/' . $filename;
    }

    // Check if admission number already exists
    $stmt = $conn->prepare("SELECT id FROM students WHERE admission_no = ?");
    $stmt->bind_param("s", $admission_no);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = "Admission number already exists";
        header("Location: add.php");
        exit();
    }
    $stmt->close();

    // Insert student record
    $stmt = $conn->prepare("INSERT INTO students (name, admission_no, year, stream, branch, phone, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $admission_no, $year, $stream, $branch, $phone, $image_path);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Student added successfully";
        header("Location: add.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to add student: " . $conn->error;
        header("Location: add.php");
        exit();
    }
    $stmt->close();
} else {
    header("Location: add.php");
    exit();
} 