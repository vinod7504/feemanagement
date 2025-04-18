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
    $fee_id = (int)$_POST['fee_id'];
    $student_id = (int)$_POST['student_id'];
    $ref_no = trim($_POST['ref_no']);
    $amount_paid = trim($_POST['amount_paid']);
    $year = trim($_POST['year']);
    $stream = trim($_POST['stream']);
    $branch = trim($_POST['branch']);
    
    // Validate required fields
    if (empty($fee_id) || empty($student_id) || empty($ref_no) || empty($amount_paid) || 
        empty($year) || empty($stream) || empty($branch)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?id=" . $fee_id);
        exit();
    }

    // Validate amount
    if (!is_numeric($amount_paid) || $amount_paid <= 0) {
        $_SESSION['error'] = "Please enter a valid amount";
        header("Location: edit.php?id=" . $fee_id);
        exit();
    }

    // Check if student exists
    $stmt = $conn->prepare("SELECT id FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        $_SESSION['error'] = "Selected student does not exist";
        header("Location: edit.php?id=" . $fee_id);
        exit();
    }
    $stmt->close();

    // Check if reference number already exists (excluding current fee record)
    $stmt = $conn->prepare("SELECT id FROM fees WHERE ref_no = ? AND id != ?");
    $stmt->bind_param("si", $ref_no, $fee_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = "Reference number already exists";
        header("Location: edit.php?id=" . $fee_id);
        exit();
    }
    $stmt->close();

    // Update fee record
    $stmt = $conn->prepare("UPDATE fees SET ref_no = ?, amount_paid = ?, year = ?, stream = ?, branch = ? WHERE id = ? AND student_id = ?");
    $stmt->bind_param("sdsssis", $ref_no, $amount_paid, $year, $stream, $branch, $fee_id, $student_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Fee payment updated successfully";
        header("Location: ../students/view_details.php?id=" . $student_id);
        exit();
    } else {
        $_SESSION['error'] = "Failed to update payment: " . $conn->error;
        header("Location: edit.php?id=" . $fee_id);
        exit();
    }
    $stmt->close();
} else {
    header("Location: ../dashboard.php");
    exit();
} 