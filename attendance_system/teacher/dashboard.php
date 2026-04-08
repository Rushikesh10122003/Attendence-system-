 <?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'teacher'){
    header("Location: ../index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Get teacher's class
$class = mysqli_query($conn,
    "SELECT * FROM classes WHERE teacher_id=$teacher_id");
$class_data = mysqli_fetch_assoc($class);

// Count students in class
$total_students = 0;
$today_present  = 0;
$today_absent   = 0;

if($class_data){
    $class_id = $class_data['id'];
    $today    = date('Y-m-d');

    $total_students = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) as total FROM students 
         WHERE class_id=$class_id"))['total'];

    $today_present = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) as total FROM attendance
         WHERE class_id=$class_id 
         AND date='$today' 
         AND status='present'"))['total'];

    $today_absent = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) as total FROM attendance
         WHERE class_id=$class_id 
         AND date='$today' 
         AND status='absent'"))['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Poppins', sans-serif;
            background:#f0f2f5;
            min-height:100vh;
        }

        /* Sidebar */
        .sidebar {
            position:fixed;
            left:0; top:0;
            width:260px;
            height:100vh;
            background:linear-gradient(180deg, #134e5e, #71b280);
            padding:30px 0;
            z-index:100;
            box-shadow:5px 0 20px rgba(0,0,0,0.3);
        }

        .sidebar-logo {
            text-align:center;
            padding:0 20px 30px 20px;
            border-bottom:1px solid rgba(255,255,255,0.15);
        }

        .sidebar-logo .logo-icon {
            font-size:50px;
            display:block;
            margin-bottom:10px;
            animation:bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform:translateY(0); }
            50%       { transform:translateY(-10px); }
        }

        .sidebar-logo h2 {
            color:white;
            font-size:16px;
            font-weight:600;
        }

        .sidebar-logo p {
            color:rgba(255,255,255,0.5);
            font-size:12px;
            margin-top:3px;
        }

        .teacher-profile {
            margin:20px;
            background:rgba(255,255,255,0.1);
            border-radius:12px;
            padding:15px;
            text-align:center;
        }

        .teacher-avatar {
            width:60px;
            height:60px;
            background:rgba(255,255,255,0.2);
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:30px;
            margin:0 auto 10px auto;
            border:2px solid rgba(255,255,255,0.3);
        }

        .teacher-profile .t-name {
            color:white;
            font-size:14px;
            font-weight:600;
        }

        .teacher-profile .t-class {
            color:rgba(255,255,255,0.6);
            font-size:12px;
            margin-top:3px;
        }

        .sidebar-menu {
            padding:10px 0;
            list-style:none;
        }

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

        .sidebar-menu li a .menu-icon { font-size:20px; }

        .sidebar-bottom {
            position:absolute;
            bottom:0; left:0;
            width:100%;
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

        .logout-btn:hover {
            background:rgba(255,0,0,0.2);
            color:#ff6b6b;
        }

        /* Main Content */
        .main-content {
            margin-left:260px;
            padding:30px;
        }

        /* Topbar */
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

        .topbar h1 {
            font-size:22px;
            color:#134e5e;
            font-weight:600;
        }

        .topbar h1 span { color:#71b280; }

        .date-badge {
            background:#e8f5e9;
            color:#2e7d32;
            padding:8px 18px;
            border-radius:20px;
            font-size:13px;
            font-weight:500;
        }

        /* Stats */
        .stats-grid {
            display:grid;
            grid-template-columns:repeat(3, 1fr);
            gap:20px;
            margin-bottom:30px;
        }

        .stat-card {
            background:white;
            border-radius:15px;
            padding:25px;
            box-shadow:0 2px 15px rgba(0,0,0,0.08);
            display:flex;
            align-items:center;
            gap:20px;
            transition:all 0.3s;
            animation:fadeInUp 0.5s ease forwards;
            opacity:0;
            position:relative;
            overflow:hidden;
        }

        .stat-card::after {
            content:'';
            position:absolute;
            top:0; right:0;
            width:80px; height:80px;
            border-radius:50%;
            opacity:0.08;
        }

        .stat-card:nth-child(1){ animation-delay:0.1s; }
        .stat-card:nth-child(1)::after { background:#134e5e; }
        .stat-card:nth-child(2){ animation-delay:0.2s; }
        .stat-card:nth-child(2)::after { background:#2e7d32; }
        .stat-card:nth-child(3){ animation-delay:0.3s; }
        .stat-card:nth-child(3)::after { background:#c62828; }

        .stat-card:hover {
            transform:translateY(-5px);
            box-shadow:0 10px 30px rgba(0,0,0,0.12);
        }

        @keyframes fadeInUp {
            from { opacity:0; transform:translateY(30px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .stat-icon {
            width:65px; height:65px;
            border-radius:15px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:30px;
            flex-shrink:0;
        }

        .stat-icon.teal   { background:#e0f2f1; }
        .stat-icon.green  { background:#e8f5e9; }
        .stat-icon.red    { background:#ffebee; }

        .stat-info h3 {
            font-size:32px;
            font-weight:700;
            color:#1a1a2e;
        }

        .stat-info p {
            font-size:13px;
            color:#888;
            margin-top:3px;
        }

        /* Action Cards */
        .section-title {
            font-size:18px;
            font-weight:600;
            color:#1a1a2e;
            margin-bottom:20px;
        }

        .actions-grid {
            display:grid;
            grid-template-columns:repeat(2, 1fr);
            gap:20px;
            margin-bottom:30px;
        }

        .action-card {
            background:white;
            border-radius:15px;
            padding:30px;
            text-decoration:none;
            box-shadow:0 2px 15px rgba(0,0,0,0.08);
            transition:all 0.3s;
            display:flex;
            align-items:center;
            gap:20px;
            animation:fadeInUp 0.5s ease forwards;
            opacity:0;
            position:relative;
            overflow:hidden;
        }

        .action-card:nth-child(1){
            animation-delay:0.4s;
            border-left:5px solid #134e5e;
        }

        .action-card:nth-child(2){
            animation-delay:0.5s;
            border-left:5px solid #71b280;
        }

        .action-card:hover {
            transform:translateY(-5px);
            box-shadow:0 15px 35px rgba(0,0,0,0.12);
        }

        .action-icon-big {
            font-size:50px;
            flex-shrink:0;
        }

        .action-text h3 {
            font-size:18px;
            font-weight:600;
            color:#1a1a2e;
            margin-bottom:5px;
        }

        .action-text p {
            font-size:13px;
            color:#888;
        }

        .action-arrow {
            margin-left:auto;
            font-size:24px;
            color:#ddd;
            transition:all 0.3s;
        }

        .action-card:hover .action-arrow {
            color:#134e5e;
            transform:translateX(5px);
        }

        /* Recent Attendance */
        .table-card {
            background:white;
            border-radius:15px;
            padding:25px;
            box-shadow:0 2px 15px rgba(0,0,0,0.08);
            animation:fadeInUp 0.5s ease 0.6s forwards;
            opacity:0;
        }

        .table-header {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:20px;
        }

        .table-header h3 {
            font-size:16px;
            font-weight:600;
            color:#1a1a2e;
        }

        .view-all {
            font-size:13px;
            color:#71b280;
            text-decoration:none;
            font-weight:500;
        }

        table { width:100%; border-collapse:collapse; }

        thead tr { background:linear-gradient(135deg, #134e5e, #71b280); }

        thead th {
            padding:13px 18px;
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

        tbody tr:hover { background:#f0fff4; }

        tbody td {
            padding:13px 18px;
            font-size:13px;
            color:#444;
        }

        .status-badge {
            padding:5px 14px;
            border-radius:20px;
            font-size:12px;
            font-weight:600;
        }

        .status-badge.present { background:#e8f5e9; color:#2e7d32; }
        .status-badge.absent  { background:#ffebee; color:#c62828; }
        .status-badge.late    { background:#fff3e0; color:#e65100; }

        .no-class {
            text-align:center;
            padding:50px;
            color:#888;
        }

        .no-class .icon { font-size:60px; margin-bottom:15px; }
    </style>
<link rel="stylesheet" href="../css/darkmode.css">
</head>
<body>
<button class="dark-toggle" id="darkToggle" onclick="toggleDarkMode()">🌙</button>
<script src="../js/darkmode.js"></script>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-logo">
        <span class="logo-icon">📚</span>
        <h2>Attendance System</h2>
        <p>Teacher Panel</p>
    </div>

    <div class="teacher-profile">
        <div class="teacher-avatar">👨‍🏫</div>
        <div class="t-name"><?php echo $_SESSION['user_name']; ?></div>
        <div class="t-class">
            <?php echo $class_data ? $class_data['class_name'] : 'No Class'; ?>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="active">
                <span class="menu-icon">🏠</span> Dashboard
            </a>
        </li>
        <li>
            <a href="take_attendance.php">
                <span class="menu-icon">📋</span> Take Attendance
            </a>
        </li>
        <li>
            <a href="view_report.php">
                <span class="menu-icon">📊</span> View Reports
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
        <h1>Teacher <span>Dashboard</span></h1>
        <div class="date-badge">
            📅 <?php echo date('D, d M Y'); ?>
        </div>
    </div>

    <?php if($class_data): ?>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon teal">👨‍🎓</div>
            <div class="stat-info">
                <h3><?php echo $total_students; ?></h3>
                <p>Total Students</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">✅</div>
            <div class="stat-info">
                <h3><?php echo $today_present; ?></h3>
                <p>Present Today</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">❌</div>
            <div class="stat-info">
                <h3><?php echo $today_absent; ?></h3>
                <p>Absent Today</p>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="section-title">⚡ Quick Actions</div>
    <div class="actions-grid">
        <a href="take_attendance.php" class="action-card">
            <div class="action-icon-big">📋</div>
            <div class="action-text">
                <h3>Take Attendance</h3>
                <p>Mark today's attendance for your class</p>
            </div>
            <div class="action-arrow">→</div>
        </a>
        <a href="view_report.php" class="action-card">
            <div class="action-icon-big">📊</div>
            <div class="action-text">
                <h3>View Reports</h3>
                <p>Check attendance reports by date</p>
            </div>
            <div class="action-arrow">→</div>
        </a>
    </div>

    <!-- Recent Attendance -->
    <div class="table-card">
        <div class="table-header">
            <h3>📋 Recent Attendance</h3>
            <a href="view_report.php" class="view-all">View All →</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Roll No</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $recent = mysqli_query($conn,
                "SELECT attendance.*, students.name, students.roll_no
                 FROM attendance
                 LEFT JOIN students ON attendance.student_id = students.id
                 WHERE attendance.class_id=$class_id
                 ORDER BY attendance.date DESC, attendance.id DESC
                 LIMIT 10");
            $i = 1;
            $found = false;
            while($a = mysqli_fetch_assoc($recent)):
                $found = true;
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td>👨‍🎓 <?php echo $a['name']; ?></td>
                <td><?php echo $a['roll_no']; ?></td>
                <td><?php echo $a['date']; ?></td>
                <td>
                    <span class="status-badge <?php echo $a['status']; ?>">
                        <?php echo strtoupper($a['status']); ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if(!$found): ?>
            <tr>
                <td colspan="5" style="text-align:center; padding:30px; color:#888;">
                    📭 No attendance records yet!
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php else: ?>
    <div class="no-class">
        <div class="icon">⚠️</div>
        <h3>No Class Assigned!</h3>
        <p>Please contact admin to assign a class to you.</p>
    </div>
    <?php endif; ?>

</div>

</body>
</html>