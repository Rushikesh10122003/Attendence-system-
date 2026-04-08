<?php
session_start();
session_unset();
session_destroy();
header("Location: http://localhost/attendance_system/index.php");
exit();
?>