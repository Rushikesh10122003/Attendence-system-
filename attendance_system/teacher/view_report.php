<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'teacher'){
    header("Location: ../index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

$class = mysqli_query($conn, "SELECT * FROM classes WHERE teacher_id=$teacher_id");
$class_data = mysqli_fetch_assoc($class);

if(!$class_data){
    die("No class assigned! Contact Admin.");
}

$class_id = $class_data['id'];
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Report</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
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
            width:700px;
            margin:40px auto;
            background:white;
            padding:30px;
            border-radius:10px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }
        .container h2 { color:#2e7d32; margin-bottom:20px; }
        .filter-group {
            display:flex;
            gap:15px;
            align-items:center;
            margin-bottom:25px;
        }
        .filter-group input {
            padding:10px;
            border:2px solid #ddd;
            border-radius:8px;
            font-size:15px;
        }
        .filter-group button {
            padding:10px 20px;
            background:#2e7d32;
            color:white;
            border:none;
            border-radius:8px;
            cursor:pointer;
            font-size:15px;
        }
        .summary {
            display:flex;
            gap:15px;
            margin-bottom:25px;
        }
        .summary-box {
            flex:1;
            padding:15px;
            border-radius:10px;
            text-align:center;
            color:white;
        }
        .summary-box h3 { font-size:30px; }
        .summary-box p { font-size:14px; margin-top:5px; }
        .green  { background:#2e7d32; }
        .red    { background:#c62828; }
        .orange { background:#e65100; }
        table { width:100%; border-collapse:collapse; }
        th { background:#2e7d32; color:white; padding:12px; text-align:center; }
        td { padding:12px; border-bottom:1px solid #ddd; text-align:center; }
        tr:hover { background:#f9f9f9; }
        .present { color:green; font-weight:bold; }
        .absent  { color:red; font-weight:bold; }
        .late    { color:orange; font-weight:bold; }
        .no-data { text-align:center; padding:30px; color:#888; font-size:18px; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>🎓 Teacher Panel</h2>
    <div>
        <a href="javascript:history.back()">← Back</a>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>📊 Attendance Report</h2>
    <h4 style="color:#555; margin-bottom:20px;">
        Class: <?php echo $class_data['class_name']; ?>
    </h4>

   <form method="GET">
    <div class="filter-group">
        <input type="date" name="date"
        value="<?php echo $selected_date; ?>">
        <button type="submit">🔍 Search</button>
        <a href="download_pdf.php?date=<?php echo $selected_date; ?>&class_id=<?php echo $class_id; ?>"
        style="
            display:inline-flex;
            align-items:center;
            gap:8px;
            background:linear-gradient(135deg, #f44336, #c62828);
            color:white;
            padding:10px 20px;
            border-radius:8px;
            text-decoration:none;
            font-size:14px;
            font-weight:600;
            font-family:'Poppins',sans-serif;
            box-shadow:0 3px 10px rgba(244,67,54,0.3);
            transition:all 0.3s;"
        onmouseover="this.style.transform='translateY(-2px)'"
        onmouseout="this.style.transform='translateY(0)'">
            📄 Download PDF
        </a>
    </div>
</form>

    <?php
    $present = 0; $absent = 0; $late = 0;

    $temp = mysqli_query($conn,
        "SELECT status, COUNT(*) as count FROM attendance
         WHERE class_id=$class_id AND date='$selected_date'
         GROUP BY status");

    while($t = mysqli_fetch_assoc($temp)){
        if($t['status'] == 'present') $present = $t['count'];
        if($t['status'] == 'absent')  $absent  = $t['count'];
        if($t['status'] == 'late')    $late    = $t['count'];
    }

    $total = $present + $absent + $late;
    ?>

    <?php if($total > 0): ?>
    <div class="summary">
        <div class="summary-box green">
            <h3><?php echo $present; ?></h3>
            <p>Present</p>
        </div>
        <div class="summary-box red">
            <h3><?php echo $absent; ?></h3>
            <p>Absent</p>
        </div>
        <div class="summary-box orange">
            <h3><?php echo $late; ?></h3>
            <p>Late</p>
        </div>
    </div>
    <?php endif; ?>

    <table>
        <tr>
            <th>#</th>
            <th>Roll No</th>
            <th>Student Name</th>
            <th>Status</th>
        </tr>
        <?php
        $attendance = mysqli_query($conn,
            "SELECT attendance.*, students.name, students.roll_no
             FROM attendance
             LEFT JOIN students ON attendance.student_id = students.id
             WHERE attendance.class_id=$class_id
             AND attendance.date='$selected_date'
             ORDER BY students.roll_no ASC");

        $i = 1;
        $found = false;
        while($a = mysqli_fetch_assoc($attendance)){
            $found = true;
        ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo $a['roll_no']; ?></td>
            <td><?php echo $a['name']; ?></td>
            <td class="<?php echo $a['status']; ?>">
                <?php echo strtoupper($a['status']); ?>
            </td>
        </tr>
        <?php } ?>

        <?php if(!$found){ ?>
        <tr>
            <td colspan="4" class="no-data">
                📭 No attendance found for this date!
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>