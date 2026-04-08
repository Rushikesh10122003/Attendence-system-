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
    "DELETE FROM attendance WHERE class_id=$id");

// Remove students from this class
mysqli_query($conn,
    "UPDATE students SET class_id=NULL WHERE class_id=$id");

// Delete class
mysqli_query($conn,
    "DELETE FROM classes WHERE id=$id");

header("Location: add_class.php?deleted=1");
exit();
?>