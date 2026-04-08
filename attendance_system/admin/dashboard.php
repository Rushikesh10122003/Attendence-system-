<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin'){
    header("Location: ../index.php");
    exit();
}

// Count stats
$total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM students"))['total'];
$total_teachers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='teacher'"))['total'];
$total_classes  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM classes"))['total'];
$total_parents  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='parent'"))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
            background:linear-gradient(180deg, #1a1a2e, #16213e, #0f3460);
            padding:30px 0;
            z-index:100;
            box-shadow:5px 0 20px rgba(0,0,0,0.3);
        }

        .sidebar-logo {
            text-align:center;
            padding:0 20px 30px 20px;
            border-bottom:1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo .logo-icon {
            font-size:50px;
            display:block;
            margin-bottom:10px;
            animation:pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform:scale(1); }
            50%       { transform:scale(1.1); }
        }

        .sidebar-logo h2 {
            color:white;
            font-size:16px;
            font-weight:600;
        }

        .sidebar-logo p {
            color:rgba(255,255,255,0.4);
            font-size:12px;
            margin-top:3px;
        }

        .sidebar-menu {
            padding:20px 0;
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
            background:rgba(255,255,255,0.1);
            border-left-color:#6c63ff;
        }

        .sidebar-menu li a .menu-icon { font-size:20px; }

        .sidebar-bottom {
            position:absolute;
            bottom:0; left:0;
            width:100%;
            padding:20px;
            border-top:1px solid rgba(255,255,255,0.1);
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
            min-height:100vh;
        }

        /* Top Bar */
        .topbar {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:30px;
            background:white;
            padding:15px 25px;
            border-radius:15px;
            box-shadow:0 2px 15px rgba(0,0,0,0.08);
        }

        .topbar h1 {
            font-size:22px;
            color:#1a1a2e;
            font-weight:600;
        }

        .topbar h1 span { color:#6c63ff; }

        .admin-info {
            display:flex;
            align-items:center;
            gap:12px;
        }

        .admin-avatar {
            width:42px;
            height:42px;
            background:linear-gradient(135deg, #6c63ff, #3ecf8e);
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:20px;
        }

        .admin-info .name {
            font-size:14px;
            font-weight:600;
            color:#1a1a2e;
        }

        .admin-info .role {
            font-size:12px;
            color:#888;
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
            box-shadow:0 2px 15px rgba(0,0,0,0.08);
            display:flex;
            align-items:center;
            gap:20px;
            transition:all 0.3s;
            animation:fadeInUp 0.6s ease forwards;
            opacity:0;
        }

        .stat-card:hover {
            transform:translateY(-5px);
            box-shadow:0 10px 30px rgba(0,0,0,0.15);
        }

        .stat-card:nth-child(1){ animation-delay:0.1s; }
        .stat-card:nth-child(2){ animation-delay:0.2s; }
        .stat-card:nth-child(3){ animation-delay:0.3s; }
        .stat-card:nth-child(4){ animation-delay:0.4s; }

        @keyframes fadeInUp {
            from { opacity:0; transform:translateY(30px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .stat-icon {
            width:60px;
            height:60px;
            border-radius:15px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:28px;
            flex-shrink:0;
        }

        .stat-icon.blue   { background:#e8eaf6; }
        .stat-icon.green  { background:#e8f5e9; }
        .stat-icon.purple { background:#f3e5f5; }
        .stat-icon.orange { background:#fff3e0; }

        .stat-info h3 {
            font-size:28px;
            font-weight:700;
            color:#1a1a2e;
        }

        .stat-info p {
            font-size:13px;
            color:#888;
            margin-top:3px;
        }

        /* Quick Actions */
        .section-title {
            font-size:18px;
            font-weight:600;
            color:#1a1a2e;
            margin-bottom:20px;
            display:flex;
            align-items:center;
            gap:10px;
        }

        .actions-grid {
            display:grid;
            grid-template-columns:repeat(4, 1fr);
            gap:20px;
            margin-bottom:30px;
        }

        .action-card {
            background:white;
            border-radius:15px;
            padding:25px 20px;
            text-align:center;
            text-decoration:none;
            box-shadow:0 2px 15px rgba(0,0,0,0.08);
            transition:all 0.3s;
            border-bottom:4px solid transparent;
            animation:fadeInUp 0.6s ease forwards;
            opacity:0;
        }

        .action-card:nth-child(1){ animation-delay:0.5s; border-bottom-color:#6c63ff; }
        .action-card:nth-child(2){ animation-delay:0.6s; border-bottom-color:#3ecf8e; }
        .action-card:nth-child(3){ animation-delay:0.7s; border-bottom-color:#ff6584; }
        .action-card:nth-child(4){ animation-delay:0.8s; border-bottom-color:#f5a623; }

        .action-card:hover {
            transform:translateY(-8px);
            box-shadow:0 15px 35px rgba(0,0,0,0.15);
        }

        .action-card .action-icon { font-size:40px; margin-bottom:12px; }

        .action-card h3 {
            font-size:15px;
            font-weight:600;
            color:#1a1a2e;
            margin-bottom:5px;
        }

        .action-card p {
            font-size:12px;
            color:#888;
        }

        /* Recent Table */
        .table-card {
            background:white;
            border-radius:15px;
            padding:25px;
            box-shadow:0 2px 15px rgba(0,0,0,0.08);
            animation:fadeInUp 0.6s ease 0.9s forwards;
            opacity:0;
        }

        table { width:100%; border-collapse:collapse; }

        thead tr {
            background:linear-gradient(135deg, #6c63ff, #3ecf8e);
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

        tbody tr:hover { background:#f8f9ff; }

        tbody td {
            padding:14px 20px;
            font-size:13px;
            color:#444;
        }

        .badge {
            padding:5px 12px;
            border-radius:20px;
            font-size:11px;
            font-weight:600;
        }

        .badge.blue   { background:#e8eaf6; color:#3949ab; }
        .badge.green  { background:#e8f5e9; color:#2e7d32; }
        .badge.purple { background:#f3e5f5; color:#6a1b9a; }
    </style>
<link rel="stylesheet" href="../css/darkmode.css">
</head>
<body>
<button class="dark-toggle" id="darkToggle" onclick="toggleDarkMode()">🌙</button>
<script src="../js/darkmode.js"></script>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-logo">
        <span class="logo-icon">🎓</span>
        <h2>Attendance System</h2>
        <p>Admin Panel</p>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="active">
                <span class="menu-icon">🏠</span> Dashboard
            </a>
        </li>
        <li>
            <a href="add_class.php">
                <span class="menu-icon">🏫</span> Add Class
            </a>
        </li>
        <li>
            <a href="add_teacher.php">
                <span class="menu-icon">👨‍🏫</span> Add Teacher
            </a>
        </li>
        <li>
            <a href="add_parent.php">
                <span class="menu-icon">👨‍👩‍👦</span> Add Parent
            </a>
        </li>
        <li>
            <a href="add_student.php">
                <span class="menu-icon">👨‍🎓</span> Add Student
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

    <!-- Top Bar -->
    <div class="topbar">
        <h1>Admin <span>Dashboard</span></h1>
        <div class="admin-info">
            <div class="admin-avatar">👨‍💼</div>
            <div>
                <div class="name"><?php echo $_SESSION['user_name']; ?></div>
                <div class="role">Administrator</div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">👨‍🎓</div>
            <div class="stat-info">
                <h3><?php echo $total_students; ?></h3>
                <p>Total Students</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">👨‍🏫</div>
            <div class="stat-info">
                <h3><?php echo $total_teachers; ?></h3>
                <p>Total Teachers</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">🏫</div>
            <div class="stat-info">
                <h3><?php echo $total_classes; ?></h3>
                <p>Total Classes</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">👨‍👩‍👦</div>
            <div class="stat-info">
                <h3><?php echo $total_parents; ?></h3>
                <p>Total Parents</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="section-title">⚡ Quick Actions</div>
    <div class="actions-grid">
        <a href="add_class.php" class="action-card">
            <div class="action-icon">🏫</div>
            <h3>Add Class</h3>
            <p>Create a new class</p>
        </a>
        <a href="add_teacher.php" class="action-card">
            <div class="action-icon">👨‍🏫</div>
            <h3>Add Teacher</h3>
            <p>Register new teacher</p>
        </a>
        <a href="add_parent.php" class="action-card">
            <div class="action-icon">👨‍👩‍👦</div>
            <h3>Add Parent</h3>
            <p>Register new parent</p>
        </a>
        <a href="add_student.php" class="action-card">
            <div class="action-icon">👨‍🎓</div>
            <h3>Add Student</h3>
            <p>Enroll new student</p>
        </a>
    </div>

    <!-- Recent Students Table -->
    <div class="section-title">👨‍🎓 Recent Students</div>
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Roll No</th>
                    <th>Class</th>
                    <th>Parent</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $students = mysqli_query($conn,
                "SELECT students.*, classes.class_name,
                 users.name as parent_name
                 FROM students
                 LEFT JOIN classes ON students.class_id = classes.id
                 LEFT JOIN users ON students.parent_id = users.id
                 ORDER BY students.id DESC LIMIT 10");
            $i = 1;
            while($s = mysqli_fetch_assoc($students)):
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td>👨‍🎓 <?php echo $s['name']; ?></td>
                <td><?php echo $s['roll_no']; ?></td>
                <td>
                    <span class="badge blue">
                        <?php echo $s['class_name']; ?>
                    </span>
                </td>
                <td><?php echo $s['parent_name']; ?></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>
<!-- Chart.js Library -->

</body>
</html>