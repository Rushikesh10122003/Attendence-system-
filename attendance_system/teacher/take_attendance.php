<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['user_id']) || 
   $_SESSION['user_role'] != 'teacher') {
    header("Location: ../index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$success = "";
$error   = "";

// Get teacher's class
$class = mysqli_query($conn, 
    "SELECT * FROM classes WHERE teacher_id=$teacher_id");
$class_data = mysqli_fetch_assoc($class);

if(!$class_data) {
    die("⚠️ No class assigned! Contact Admin.");
}

$class_id = $class_data['id'];

// Get students of this class
$students = mysqli_query($conn, 
    "SELECT * FROM students WHERE class_id=$class_id");

// Save attendance
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];

    // Check if attendance already taken for this date
    $check = mysqli_query($conn, 
        "SELECT * FROM attendance 
         WHERE class_id=$class_id AND date='$date'");

    if(mysqli_num_rows($check) > 0) {
        $error = "Attendance already taken for this date!";
    } else {
        foreach($_POST['status'] as $student_id => $status) {
            $q = "INSERT INTO attendance 
                  (student_id, class_id, date, status, marked_by)
                  VALUES 
                  ($student_id,$class_id,'$date','$status',$teacher_id)";
            mysqli_query($conn, $q);
        }
        $success = "Attendance saved successfully!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Take Attendance</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box;
            font-family:Arial, sans-serif; }
        body { background:#f0f2f5; }
        .navbar {
            background:#2e7d32;
            padding:15px 30px;
            color:white;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .navbar h2 { font-size:20px; }
        .navbar a {
            color:white;
            text-decoration:none;
            background:#ff4444;
            padding:8px 15px;
            border-radius:5px;
            margin-left:10px;
        }
        .container {
            width:650px;
            margin:40px auto;
            background:white;
            padding:30px;
            border-radius:10px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }
        .container h2 {
            color:#2e7d32;
            margin-bottom:20px;
        }
        .date-group {
            margin-bottom:20px;
        }
        .date-group label {
            display:block;
            margin-bottom:5px;
            color:#555;
        }
        .date-group input {
            padding:10px;
            border:2px solid #ddd;
            border-radius:8px;
            font-size:15px;
            width:250px;
        }
        table { width:100%; border-collapse:collapse; 
                margin-top:20px; }
        th {
            background:#2e7d32;
            color:white;
            padding:12px;
            text-align:center;
        }
        td {
            padding:12px;
            border-bottom:1px solid #ddd;
            text-align:center;
        }
        tr:hover { background:#f9f9f9; }
        .present { accent-color:green; }
        .absent  { accent-color:red; }
        .late    { accent-color:orange; }
        button {
            margin-top:20px;
            width:100%;
            padding:12px;
            background:#2e7d32;
            color:white;
            border:none;
            border-radius:8px;
            font-size:16px;
            cursor:pointer;
        }
        button:hover { background:#1b5e20; }
        .success {
            background:#e0ffe0;
            color:green;
            padding:10px;
            border-radius:8px;
            margin-bottom:15px;
        }
        .error {
            background:#ffe0e0;
            color:red;
            padding:10px;
            border-radius:8px;
            margin-bottom:15px;
        }
    </style>
<link rel="stylesheet" href="../css/darkmode.css">
</head>
<body>
<button class="dark-toggle" id="darkToggle" onclick="toggleDarkMode()">🌙</button>
<script src="../js/darkmode.js"></script>

<div class="navbar">
    <h2>🎓 Teacher Panel</h2>
    <div>
        <a href="javascript:history.back()">← Back</a>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>📋 Take Attendance</h2>
    <h4 style="color:#555; margin-bottom:20px;">
        Class: <?php echo $class_data['class_name']; ?>
    </h4>

    <?php if($success != ""): ?>
        <div class="success">✅ <?php echo $success; ?></div>
    <?php endif; ?>

    <?php if($error != ""): ?>
        <div class="error">❌ <?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="date-group">
            <label>Select Date</label>
            <input type="date" name="date" 
            value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <table>
            <tr>
                <th>#</th>
                <th>Roll No</th>
                <th>Student Name</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Late</th>
            </tr>
            <?php 
            $i = 1;
            // Reset students query
            $students = mysqli_query($conn, 
                "SELECT * FROM students WHERE class_id=$class_id");
            while($s = mysqli_fetch_assoc($students)): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $s['roll_no']; ?></td>
                <td><?php echo $s['name']; ?></td>
                <td>
                    <input class="present" type="radio" 
                    name="status[<?php echo $s['id']; ?>]" 
                    value="present" checked>
                </td>
                <td>
                    <input class="absent" type="radio" 
                    name="status[<?php echo $s['id']; ?>]" 
                    value="absent">
                </td>
                <td>
                    <input class="late" type="radio" 
                    name="status[<?php echo $s['id']; ?>]" 
                    value="late">
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <button type="submit">💾 Save Attendance</button>
    </form>
</div>

</body>
</html>