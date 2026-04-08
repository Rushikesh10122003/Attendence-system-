<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['user_id']) ||
   $_SESSION['user_role'] != 'admin'){
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'];

// Remove teacher from classes first
mysqli_query($conn,
    "UPDATE classes SET teacher_id=NULL WHERE teacher_id=$id");

// Delete teacher
mysqli_query($conn,
    "DELETE FROM users WHERE id=$id AND role='teacher'");

header("Location: add_teacher.php?deleted=1");
exit();
?>