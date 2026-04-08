<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['user_id']) ||
   $_SESSION['user_role'] != 'admin'){
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'];

// Delete attendance records first
mysqli_query($conn,
    "DELETE FROM attendance WHERE student_id=$id");

// Delete student
mysqli_query($conn,
    "DELETE FROM students WHERE id=$id");

header("Location: add_student.php?deleted=1");
exit();
?>