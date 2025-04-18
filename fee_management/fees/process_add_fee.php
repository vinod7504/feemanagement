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
    $student_id = trim($_POST['student_id']);
    $ref_no = trim($_POST['ref_no']);
    $amount_paid = trim($_POST['amount_paid']);
    $year = trim($_POST['year']);
    $stream = trim($_POST['stream']);
    $branch = trim($_POST['branch']);
    
    // Validate required fields
    if (empty($student_id) || empty($ref_no) || empty($amount_paid) || empty($year) || 
        empty($stream) || empty($branch)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        exit();
    }

    // Validate amount
    if (!is_numeric($amount_paid) || $amount_paid <= 0) {
        $_SESSION['error'] = "Please enter a valid amount";
        header("Location: add.php");
        exit();
    }

    // Check if student exists
    $stmt = $conn->prepare("SELECT id FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        $_SESSION['error'] = "Selected student does not exist";
        header("Location: add.php");
        exit();
    }
    $stmt->close();

    // Check if reference number already exists
    $stmt = $conn->prepare("SELECT id FROM fees WHERE ref_no = ?");
    $stmt->bind_param("s", $ref_no);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = "Reference number already exists";
        header("Location: add.php");
        exit();
    }
    $stmt->close();

    // Get current timestamp
    $payment_date = date('Y-m-d H:i:s');

    // Insert fee record
    $stmt = $conn->prepare("INSERT INTO fees (student_id, ref_no, amount_paid, payment_date, year, stream, branch) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdssss", $student_id, $ref_no, $amount_paid, $payment_date, $year, $stream, $branch);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Fee payment recorded successfully";
        header("Location: add.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to record payment: " . $conn->error;
        header("Location: add.php");
        exit();
    }
    $stmt->close();
} else {
    header("Location: add.php");
    exit();
} 