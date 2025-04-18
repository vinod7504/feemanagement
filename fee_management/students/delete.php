<?php
session_start();
require_once '../config/db_connect.php';

// Check if user is not logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id'])) {
    $student_id = (int)$_GET['id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete associated fees first (due to foreign key constraint)
        $stmt = $conn->prepare("DELETE FROM fees WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();
        
        // Delete student
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            // Delete student image if exists
            $stmt = $conn->prepare("SELECT image_path FROM students WHERE id = ?");
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $student = $result->fetch_assoc();
                if (!empty($student['image_path'])) {
                    $image_path = '../' . $student['image_path'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
            }
            
            $conn->commit();
            $_SESSION['success'] = "Student deleted successfully";
        } else {
            throw new Exception("Student not found");
        }
        $stmt->close();
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Failed to delete student: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid request";
}

header("Location: view.php");
exit(); 