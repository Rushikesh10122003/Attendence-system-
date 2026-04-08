<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'parent'){
    header("Location: ../index.php");
    exit();
}

$parent_id = $_SESSION['user_id'];

$student = mysqli_query($conn,
    "SELECT students.*, classes.class_name
     FROM students
     LEFT JOIN classes ON students.class_id = classes.id
     WHERE students.parent_id=$parent_id");
$child = mysqli_fetch_assoc($student);

$week_start = date('Y-m-d', strtotime('monday this week'));
$week_end   = date('Y-m-d', strtotime('sunday this week'));

$present = 0; $absent = 0; $late = 0;

if($child){
    $student_id = $child['id'];
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
}

$total = $present + $absent + $late;
$percent = $total > 0 ? round(($present/$total)*100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parent Dashboard</title>
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
            background:linear-gradient(180deg, #4a148c, #7b1fa2, #9c27b0);
            padding:30px 0;
            z-index:100;
            box-shadow:5px 0 20px rgba(0,0,0,0.3);
        }

        .sidebar-logo {
            text-align:center;
            padding:0 20px 25px 20px;
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

        /* Child Info in Sidebar */
        .child-sidebar-info {
            margin:20px;
            background:rgba(255,255,255,0.1);
            border-radius:12px;
            padding:15px;
            text-align:center;
        }

        .child-avatar {
            width:65px; height:65px;
            background:rgba(255,255,255,0.2);
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:32px;
            margin:0 auto 10px auto;
            border:2px solid rgba(255,255,255,0.4);
        }

        .child-sidebar-info .c-name {
            color:white;
            font-size:14px;
            font-weight:600;
        }

        .child-sidebar-info .c-class {
            color:rgba(255,255,255,0.6);
            font-size:12px;
            margin-top:3px;
        }

        .child-sidebar-info .c-roll {
            color:rgba(255,255,255,0.5);
            font-size:11px;
            margin-top:2px;
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
            color:#4a148c;
            font-weight:600;
        }

        .topbar h1 span { color:#9c27b0; }

        .parent-info {
            display:flex;
            align-items:center;
            gap:12px;
        }

        .parent-avatar {
            width:42px; height:42px;
            background:linear-gradient(135deg, #4a148c, #9c27b0);
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:20px;
        }

        .parent-info .p-name {
            font-size:14px;
            font-weight:600;
            color:#1a1a2e;
        }

        .parent-info .p-role {
            font-size:12px;
            color:#888;
        }

        /* Welcome Banner */
        .welcome-banner {
            background:linear-gradient(135deg, #4a148c, #9c27b0);
            border-radius:15px;
            padding:30px;
            margin-bottom:30px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            animation:fadeInUp 0.5s ease forwards;
            position:relative;
            overflow:hidden;
        }

        .welcome-banner::before {
            content:'🎓';
            position:absolute;
            right:30px;
            font-size:120px;
            opacity:0.1;
        }

        .welcome-banner h2 {
            color:white;
            font-size:22px;
            margin-bottom:8px;
        }

        .welcome-banner p {
            color:rgba(255,255,255,0.7);
            font-size:14px;
        }

        .week-badge {
            background:rgba(255,255,255,0.15);
            color:white;
            padding:10px 20px;
            border-radius:10px;
            font-size:13px;
            text-align:center;
        }

        .week-badge strong {
            display:block;
            font-size:16px;
            margin-top:3px;
        }

        @keyframes fadeInUp {
            from { opacity:0; transform:translateY(30px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* Stats Cards */
        .stats-grid {
            display:grid;
            grid-template-columns:repeat(4, 1fr);
            gap:20px;
            margin-bottom:30px;
        }

        .stat-card {
            background:white;
            border-radius:15px;
            padding:25px;
            text-align:center;
            box-shadow:0 2px 15px rgba(0,0,0,0.08);
            transition:all 0.3s;
            animation:fadeInUp 0.5s ease forwards;
            opacity:0;
            position:relative;
            overflow:hidden;
        }

        .stat-card:nth-child(1){ animation-delay:0.2s; }
        .stat-card:nth-child(2){ animation-delay:0.3s; }
        .stat-card:nth-child(3){ animation-delay:0.4s; }
        .stat-card:nth-child(4){ animation-delay:0.5s; }

        .stat-card:hover {
            transform:translateY(-5px);
            box-shadow:0 10px 30px rgba(0,0,0,0.12);
        }

        .stat-emoji { font-size:35px; margin-bottom:10px; }

        .stat-number {
            font-size:35px;
            font-weight:700;
            margin-bottom:5px;
        }

        .stat-label {
            font-size:13px;
            color:#888;
        }

        .stat-card.present .stat-number { color:#2e7d32; }
        .stat-card.absent  .stat-number { color:#c62828; }
        .stat-card.late    .stat-number { color:#e65100; }
        .stat-card.percent .stat-number { color:#4a148c; }

        .stat-card.present { border-top:4px solid #2e7d32; }
        .stat-card.absent  { border-top:4px solid #c62828; }
        .stat-card.late    { border-top:4px solid #e65100; }
        .stat-card.percent { border-top:4px solid #4a148c; }

        /* Progress Bar */
        .progress-card {
            background:white;
            border-radius:15px;
            padding:25px;
            box-shadow:0 2px 15px rgba(0,0,0,0.08);
            margin-bottom:30px;
            animation:fadeInUp 0.5s ease 0.6s forwards;
            opacity:0;
        }

        .progress-card h3 {
            font-size:16px;
            font-weight:600;
            color:#1a1a2e;
            margin-bottom:20px;
        }

        .progress-bar-container {
            background:#f0f0f0;
            border-radius:10px;
            height:20px;
            overflow:hidden;
            margin-bottom:10px;
        }

        .progress-bar {
            height:100%;
            border-radius:10px;
            background:linear-gradient(90deg, #4a148c, #9c27b0);
            transition:width 1s ease;
            animation:fillBar 1.5s ease 0.8s forwards;
            width:0;
        }

        @keyframes fillBar {
            to { width:<?php echo $percent; ?>%; }
        }

        .progress-labels {
            display:flex;
            justify-content:space-between;
            font-size:13px;
            color:#888;
        }

        .progress-percent {
            font-size:28px;
            font-weight:700;
            color:#4a148c;
            text-align:center;
            margin-bottom:10px;
        }

        /* Action Card */
        .action-card {
            background:linear-gradient(135deg, #4a148c, #9c27b0);
            border-radius:15px;
            padding:30px;
            text-decoration:none;
            box-shadow:0 5px 20px rgba(74,20,140,0.4);
            transition:all 0.3s;
            display:flex;
            align-items:center;
            gap:20px;
            animation:fadeInUp 0.5s ease 0.7s forwards;
            opacity:0;
            margin-bottom:30px;
        }

        .action-card:hover {
            transform:translateY(-5px);
            box-shadow:0 15px 35px rgba(74,20,140,0.5);
        }

        .action-icon-big { font-size:50px; }

        .action-text h3 {
            color:white;
            font-size:20px;
            font-weight:600;
            margin-bottom:5px;
        }

        .action-text p {
            color:rgba(255,255,255,0.7);
            font-size:14px;
        }

        .action-arrow {
            margin-left:auto;
            color:rgba(255,255,255,0.5);
            font-size:30px;
            transition:all 0.3s;
        }

        .action-card:hover .action-arrow {
            color:white;
            transform:translateX(10px);
        }

        /* Recent Table */
        .table-card {
            background:white;
            border-radius:15px;
            padding:25px;
            box-shadow:0 2px 15px rgba(0,0,0,0.08);
            animation:fadeInUp 0.5s ease 0.8s forwards;
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
            color:#9c27b0;
            text-decoration:none;
            font-weight:500;
        }

        table { width:100%; border-collapse:collapse; }

        thead tr {
            background:linear-gradient(135deg, #4a148c, #9c27b0);
        }

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

        tbody tr:hover { background:#f9f0ff; }

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

        .no-child {
            text-align:center;
            padding:60px;
            color:#888;
        }

        .no-child .icon { font-size:70px; margin-bottom:15px; }
    </style>
<link rel="stylesheet" href="../css/darkmode.css">
</head>
<body>
<button class="dark-toggle" id="darkToggle" onclick="toggleDarkMode()">🌙</button>
<script src="../js/darkmode.js"></script>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-logo">
        <span class="logo-icon">👨‍👩‍👦</span>
        <h2>Attendance System</h2>
        <p>Parent Panel</p>
    </div>

    <?php if($child): ?>
    <div class="child-sidebar-info">
        <div class="child-avatar">👨‍🎓</div>
        <div class="c-name"><?php echo $child['name']; ?></div>
        <div class="c-class">
            🏫 <?php echo $child['class_name']; ?>
        </div>
        <div class="c-roll">
            Roll No: <?php echo $child['roll_no']; ?>
        </div>
    </div>
    <?php endif; ?>

    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="active">
                <span class="menu-icon">🏠</span> Dashboard
            </a>
        </li>
        <li>
            <a href="view_attendance.php">
                <span class="menu-icon">📋</span> View Attendance
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
        <h1>Parent <span>Dashboard</span></h1>
        <div class="parent-info">
            <div class="parent-avatar">👨‍👩‍👦</div>
            <div>
                <div class="p-name"><?php echo $_SESSION['user_name']; ?></div>
                <div class="p-role">Parent</div>
            </div>
        </div>
    </div>

    <?php if($child): ?>

    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div>
            <h2>👋 Welcome, <?php echo $_SESSION['user_name']; ?>!</h2>
            <p>Here is your child's attendance summary for this week</p>
        </div>
        <div class="week-badge">
            📅 This Week
            <strong>
                <?php echo date('d M', strtotime($week_start)); ?>
                -
                <?php echo date('d M Y', strtotime($week_end)); ?>
            </strong>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card present">
            <div class="stat-emoji">✅</div>
            <div class="stat-number"><?php echo $present; ?></div>
            <div class="stat-label">Days Present</div>
        </div>
        <div class="stat-card absent">
            <div class="stat-emoji">❌</div>
            <div class="stat-number"><?php echo $absent; ?></div>
            <div class="stat-label">Days Absent</div>
        </div>
        <div class="stat-card late">
            <div class="stat-emoji">⏰</div>
            <div class="stat-number"><?php echo $late; ?></div>
            <div class="stat-label">Days Late</div>
        </div>
        <div class="stat-card percent">
            <div class="stat-emoji">📊</div>
            <div class="stat-number"><?php echo $percent; ?>%</div>
            <div class="stat-label">Attendance Rate</div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="progress-card">
        <h3>📈 Weekly Attendance Progress</h3>
        <div class="progress-percent"><?php echo $percent; ?>%</div>
        <div class="progress-bar-container">
            <div class="progress-bar"></div>
        </div>
        <div class="progress-labels">
            <span>0%</span>
            <span>
                <?php echo $present; ?> out of 
                <?php echo $total > 0 ? $total : 5; ?> days present
            </span>
            <span>100%</span>
        </div>
    </div>

    <!-- View Attendance Button -->
    <a href="view_attendance.php" class="action-card">
        <div class="action-icon-big">📋</div>
        <div class="action-text">
            <h3>View Full Attendance</h3>
            <p>Check detailed weekly attendance records for your child</p>
        </div>
        <div class="action-arrow">→</div>
    </a>

    <!-- Recent Records -->
    <div class="table-card">
        <div class="table-header">
            <h3>📅 Recent Attendance Records</h3>
            <a href="view_attendance.php" class="view-all">
                View All →
            </a>
        </div>
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
            $recent = mysqli_query($conn,
                "SELECT * FROM attendance
                 WHERE student_id={$child['id']}
                 ORDER BY date DESC LIMIT 7");
            $i = 1;
            $found = false;
            while($a = mysqli_fetch_assoc($recent)):
                $found = true;
                $day = date('l', strtotime($a['date']));
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $a['date']; ?></td>
                <td><?php echo $day; ?></td>
                <td>
                    <span class="status-badge <?php echo $a['status']; ?>">
                        <?php echo strtoupper($a['status']); ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if(!$found): ?>
            <tr>
                <td colspan="4" style="text-align:center;
                    padding:30px; color:#888;">
                    📭 No attendance records yet!
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php else: ?>
    <div class="no-child">
        <div class="icon">⚠️</div>
        <h3>No Child Assigned!</h3>
        <p>Please contact admin to link your child to your account.</p>
    </div>
    <?php endif; ?>

</div>

</body>
</html>