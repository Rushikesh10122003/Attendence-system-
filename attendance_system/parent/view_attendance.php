<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['user_id']) ||
   $_SESSION['user_role'] != 'parent'){
    header("Location: ../index.php");
    exit();
}

$parent_id  = $_SESSION['user_id'];

$student = mysqli_query($conn,
    "SELECT students.*, classes.class_name
     FROM students
     LEFT JOIN classes ON students.class_id = classes.id
     WHERE students.parent_id=$parent_id");
$child = mysqli_fetch_assoc($student);

if(!$child){ die("No child found! Contact Admin."); }

$student_id = $child['id'];

$week_start = isset($_GET['week_start']) ?
    $_GET['week_start'] :
    date('Y-m-d', strtotime('monday this week'));
$week_end = date('Y-m-d',
    strtotime($week_start . ' +6 days'));

$present = 0; $absent = 0; $late = 0;
$temp = mysqli_query($conn,
    "SELECT status, COUNT(*) as count FROM attendance
     WHERE student_id=$student_id
     AND date BETWEEN '$week_start' AND '$week_end'
     GROUP BY status");
while($t = mysqli_fetch_assoc($temp)){
    if($t['status'] == 'present') $present = $t['count'];
    if($t['status'] == 'absent')  $absent  = $t['count'];
    if($t['status'] == 'late')    $late    = $t['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Attendance</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Poppins', sans-serif;
            background:#f0f2f5;
            min-height:100vh;
        }
        .sidebar {
            position:fixed;
            left:0; top:0;
            width:260px; height:100vh;
            background:linear-gradient(180deg, #4a148c, #7b1fa2, #9c27b0);
            padding:30px 0;
            z-index:100;
        }
        .sidebar-logo {
            text-align:center;
            padding:0 20px 25px 20px;
            border-bottom:1px solid rgba(255,255,255,0.15);
        }
        .sidebar-logo span { font-size:45px; display:block; margin-bottom:8px; }
        .sidebar-logo h2 { color:white; font-size:16px; }
        .sidebar-logo p  { color:rgba(255,255,255,0.5); font-size:12px; }
        .child-sidebar-info {
            margin:20px;
            background:rgba(255,255,255,0.1);
            border-radius:12px;
            padding:15px;
            text-align:center;
        }
        .child-avatar {
            width:60px; height:60px;
            background:rgba(255,255,255,0.2);
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:28px;
            margin:0 auto 8px auto;
        }
        .c-name  { color:white; font-size:14px; font-weight:600; }
        .c-class { color:rgba(255,255,255,0.6); font-size:12px; margin-top:3px; }
        .sidebar-menu { padding:10px 0; list-style:none; }
        .sidebar-menu li a {
            display:flex;
            align-items:center;
            gap:15px;
            padding:14px 25px;
            color:rgba(255,255,255,0.6);
            text-decoration:none;
            font-size:14px;
            transition:all 0.3s;
            border-left:3px solid transparent;
        }
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            color:white;
            background:rgba(255,255,255,0.15);
            border-left-color:white;
        }
        .sidebar-bottom {
            position:absolute;
            bottom:0; left:0; width:100%;
            padding:20px;
            border-top:1px solid rgba(255,255,255,0.15);
        }
        .logout-btn {
            display:flex;
            align-items:center;
            gap:10px;
            color:rgba(255,255,255,0.6);
            text-decoration:none;
            font-size:14px;
            padding:12px 15px;
            border-radius:10px;
            transition:all 0.3s;
        }
        .logout-btn:hover { background:rgba(255,0,0,0.2); color:#ff6b6b; }

        .main-content { margin-left:260px; padding:30px; }

        .topbar {
            display:flex;
            justify-content:space-between;
            align-items:center;
            background:white;
            padding:15px 25px;
            border-radius:15px;
            box-shadow:0 2px 15px rgba(0,0,0,0.08);
            margin-bottom:30px;
        }
        .topbar h1 { font-size:22px; color:#4a148c; font-weight:600; }
        .topbar h1 span { color:#9c27b0; }

        .container {
            background:white;
            border-radius:15px;
            padding:30px;
            box-shadow:0 2px 15px rgba(0,0,0,0.08);
        }

        .child-name {
            color:#555;
            margin-bottom:25px;
            font-size:15px;
            padding:15px;
            background:#f9f0ff;
            border-radius:10px;
            border-left:4px solid #9c27b0;
        }

        .filter-group {
            display:flex;
            gap:15px;
            align-items:center;
            margin-bottom:25px;
        }
        .filter-group input {
            padding:10px 15px;
            border:2px solid #ddd;
            border-radius:10px;
            font-size:14px;
            font-family:'Poppins', sans-serif;
        }
        .filter-group input:focus {
            border-color:#9c27b0;
            outline:none;
        }
        .filter-group button {
            padding:10px 20px;
            background:linear-gradient(135deg, #4a148c, #9c27b0);
            color:white;
            border:none;
            border-radius:10px;
            cursor:pointer;
            font-size:14px;
            font-family:'Poppins', sans-serif;
        }

        .summary {
            display:flex;
            gap:15px;
            margin-bottom:25px;
        }
        .summary-box {
            flex:1;
            padding:20px;
            border-radius:12px;
            text-align:center;
            color:white;
        }
        .summary-box h3 { font-size:32px; font-weight:700; }
        .summary-box p  { font-size:13px; margin-top:5px; opacity:0.9; }
        .s-green  { background:linear-gradient(135deg, #2e7d32, #4caf50); }
        .s-red    { background:linear-gradient(135deg, #c62828, #ef5350); }
        .s-orange { background:linear-gradient(135deg, #e65100, #ff9800); }

        .week-info {
            color:#888;
            font-size:13px;
            margin-bottom:20px;
            padding:10px 15px;
            background:#f8f8f8;
            border-radius:8px;
        }

        table { width:100%; border-collapse:collapse; }
        thead tr {
            background:linear-gradient(135deg, #4a148c, #9c27b0);
        }
        thead th {
            padding:14px 20px;
            color:white;
            font-size:13px;
            font-weight:500;
            text-align:left;
        }
        thead th:first-child { border-radius:10px 0 0 10px; }
        thead th:last-child  { border-radius:0 10px 10px 0; }
        tbody tr {
            border-bottom:1px solid #f0f0f0;
            transition:all 0.2s;
        }
        tbody tr:hover { background:#f9f0ff; }
        tbody td { padding:14px 20px; font-size:13px; color:#444; }

        .status-badge {
            padding:6px 16px;
            border-radius:20px;
            font-size:12px;
            font-weight:600;
        }
        .status-badge.present { background:#e8f5e9; color:#2e7d32; }
        .status-badge.absent  { background:#ffebee; color:#c62828; }
        .status-badge.late    { background:#fff3e0; color:#e65100; }

        .no-data {
            text-align:center;
            padding:50px;
            color:#888;
        }
        .no-data .icon { font-size:60px; margin-bottom:15px; }
    </style>
<link rel="stylesheet" href="../css/darkmode.css">
</head>
<body>
<button class="dark-toggle" id="darkToggle" onclick="toggleDarkMode()">🌙</button>
<script src="../js/darkmode.js"></script>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-logo">
        <span>👨‍👩‍👦</span>
        <h2>Attendance System</h2>
        <p>Parent Panel</p>
    </div>

    <div class="child-sidebar-info">
        <div class="child-avatar">👨‍🎓</div>
        <div class="c-name"><?php echo $child['name']; ?></div>
        <div class="c-class">🏫 <?php echo $child['class_name']; ?></div>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php">
                <span>🏠</span> Dashboard
            </a>
        </li>
        <li>
            <a href="view_attendance.php" class="active">
                <span>📋</span> View Attendance
            </a>
        </li>
    </ul>

    <div class="sidebar-bottom">
        <a href="../logout.php" class="logout-btn">
            🚪 Logout
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">

    <div class="topbar">
        <h1>Weekly <span>Attendance</span></h1>
        <a href="dashboard.php" style="
            background:#f3e5f5;
            color:#6a1b9a;
            padding:8px 18px;
            border-radius:20px;
            text-decoration:none;
            font-size:13px;
            font-weight:500;">
            ← Back to Dashboard
        </a>
    </div>

    <div class="container">
        <div class="child-name">
            👨‍🎓 <strong><?php echo $child['name']; ?></strong>
            &nbsp;|&nbsp;
            🏫 Class: <strong><?php echo $child['class_name']; ?></strong>
            &nbsp;|&nbsp;
            🔢 Roll No: <strong><?php echo $child['roll_no']; ?></strong>
        </div>

        <!-- Filter -->
        <form method="GET">
            <div class="filter-group">
                <input type="date" name="week_start"
                value="<?php echo $week_start; ?>">
                <button type="submit">🔍 Search Week</button>
            </div>
        </form>

        <div class="week-info">
            📅 Showing attendance from
            <strong><?php echo date('d M Y', strtotime($week_start)); ?></strong>
            to
            <strong><?php echo date('d M Y', strtotime($week_end)); ?></strong>
        </div>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-box s-green">
                <h3><?php echo $present; ?></h3>
                <p>✅ Present</p>
            </div>
            <div class="summary-box s-red">
                <h3><?php echo $absent; ?></h3>
                <p>❌ Absent</p>
            </div>
            <div class="summary-box s-orange">
                <h3><?php echo $late; ?></h3>
                <p>⏰ Late</p>
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $attendance = mysqli_query($conn,
                "SELECT * FROM attendance
                 WHERE student_id=$student_id
                 AND date BETWEEN '$week_start' AND '$week_end'
                 ORDER BY date ASC");
            $i = 1;
            $found = false;
            while($a = mysqli_fetch_assoc($attendance)){
                $found = true;
                $day = date('l', strtotime($a['date']));
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo date('d M Y', strtotime($a['date'])); ?></td>
                <td><?php echo $day; ?></td>
                <td>
                    <span class="status-badge <?php echo $a['status']; ?>">
                        <?php echo strtoupper($a['status']); ?>
                    </span>
                </td>
            </tr>
            <?php } ?>

            <?php if(!$found){ ?>
            <tr>
                <td colspan="4">
                    <div class="no-data">
                        <div class="icon">📭</div>
                        <h3>No attendance found!</h3>
                        <p>Try selecting a different week</p>
                    </div>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>