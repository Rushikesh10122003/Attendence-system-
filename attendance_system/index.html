<?php
session_start();
include('includes/db.php');

$error = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $query  = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user   = mysqli_fetch_assoc($result);

    if($user && password_verify($password, $user['password'])){
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];

        if($user['role'] == 'admin')   header("Location: admin/dashboard.php");
        if($user['role'] == 'teacher') header("Location: teacher/dashboard.php");
        if($user['role'] == 'parent')  header("Location: parent/dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Poppins', sans-serif;
            min-height:100vh;
            display:flex;
            background:#0f0c29;
            overflow:hidden;
        }

        /* Animated Background */
        .bg-animation {
            position:fixed;
            top:0; left:0;
            width:100%; height:100%;
            z-index:0;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
        }

        .circle {
            position:absolute;
            border-radius:50%;
            animation:float 6s infinite ease-in-out;
            opacity:0.15;
        }
        .circle:nth-child(1){
            width:400px; height:400px;
            background:#6c63ff;
            top:-100px; left:-100px;
            animation-delay:0s;
        }
        .circle:nth-child(2){
            width:300px; height:300px;
            background:#3ecf8e;
            bottom:-80px; right:-80px;
            animation-delay:2s;
        }
        .circle:nth-child(3){
            width:200px; height:200px;
            background:#ff6584;
            top:50%; left:50%;
            animation-delay:4s;
        }

        @keyframes float {
            0%, 100% { transform:translateY(0) scale(1); }
            50%       { transform:translateY(-30px) scale(1.05); }
        }

        /* Left Side */
        .left-panel {
            flex:1;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            padding:60px;
            position:relative;
            z-index:1;
        }

        .left-panel .school-icon {
            font-size:100px;
            margin-bottom:20px;
            animation:bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform:translateY(0); }
            50%       { transform:translateY(-15px); }
        }

        .left-panel h1 {
            color:white;
            font-size:38px;
            font-weight:700;
            text-align:center;
            line-height:1.3;
        }

        .left-panel h1 span {
            color:#6c63ff;
        }

        .left-panel p {
            color:rgba(255,255,255,0.6);
            font-size:16px;
            margin-top:15px;
            text-align:center;
            max-width:400px;
            line-height:1.7;
        }

        .features {
            margin-top:40px;
            display:flex;
            flex-direction:column;
            gap:15px;
            width:100%;
            max-width:400px;
        }

        .feature-item {
            background:rgba(255,255,255,0.05);
            border:1px solid rgba(255,255,255,0.1);
            border-radius:12px;
            padding:15px 20px;
            display:flex;
            align-items:center;
            gap:15px;
            color:white;
            font-size:14px;
            transition:all 0.3s;
        }

        .feature-item:hover {
            background:rgba(108,99,255,0.2);
            border-color:#6c63ff;
            transform:translateX(10px);
        }

        .feature-item .icon { font-size:25px; }

        /* Right Side - Login Form */
        .right-panel {
            width:480px;
            background:white;
            display:flex;
            flex-direction:column;
            justify-content:center;
            padding:60px 50px;
            position:relative;
            z-index:1;
            animation:slideIn 0.8s ease;
        }

        @keyframes slideIn {
            from { transform:translateX(100px); opacity:0; }
            to   { transform:translateX(0); opacity:1; }
        }

        .right-panel::before {
            content:'';
            position:absolute;
            top:0; left:0;
            width:5px; height:100%;
            background:linear-gradient(to bottom, #6c63ff, #3ecf8e);
        }

        .login-title {
            font-size:28px;
            font-weight:700;
            color:#1a1a2e;
            margin-bottom:8px;
        }

        .login-subtitle {
            color:#888;
            font-size:14px;
            margin-bottom:35px;
        }

        .error-box {
            background:#fff0f0;
            border:1px solid #ffcdd2;
            border-left:4px solid #f44336;
            border-radius:8px;
            padding:12px 15px;
            margin-bottom:20px;
            color:#c62828;
            font-size:14px;
            display:flex;
            align-items:center;
            gap:10px;
            animation:shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform:translateX(0); }
            25%       { transform:translateX(-10px); }
            75%       { transform:translateX(10px); }
        }

        .input-group {
            margin-bottom:20px;
            position:relative;
        }

        .input-group label {
            display:block;
            font-size:13px;
            font-weight:600;
            color:#444;
            margin-bottom:8px;
            text-transform:uppercase;
            letter-spacing:0.5px;
        }

        .input-wrapper {
            position:relative;
        }

        .input-wrapper .input-icon {
            position:absolute;
            left:15px;
            top:50%;
            transform:translateY(-50%);
            font-size:18px;
            color:#aaa;
        }

        .input-group input {
            width:100%;
            padding:14px 15px 14px 45px;
            border:2px solid #eee;
            border-radius:12px;
            font-size:15px;
            font-family:'Poppins', sans-serif;
            transition:all 0.3s;
            outline:none;
            background:#f8f9ff;
        }

        .input-group input:focus {
            border-color:#6c63ff;
            background:white;
            box-shadow:0 0 0 4px rgba(108,99,255,0.1);
        }

        .login-btn {
            width:100%;
            padding:15px;
            background:linear-gradient(135deg, #6c63ff, #3ecf8e);
            color:white;
            border:none;
            border-radius:12px;
            font-size:16px;
            font-weight:600;
            font-family:'Poppins', sans-serif;
            cursor:pointer;
            transition:all 0.3s;
            margin-top:10px;
            position:relative;
            overflow:hidden;
        }

        .login-btn:hover {
            transform:translateY(-2px);
            box-shadow:0 10px 30px rgba(108,99,255,0.4);
        }

        .login-btn:active {
            transform:translateY(0);
        }

        .login-btn::after {
            content:'';
            position:absolute;
            top:50%; left:50%;
            width:0; height:0;
            background:rgba(255,255,255,0.3);
            border-radius:50%;
            transform:translate(-50%, -50%);
            transition:width 0.6s, height 0.6s;
        }

        .login-btn:hover::after {
            width:300px;
            height:300px;
        }

        .roles-info {
            margin-top:30px;
            padding-top:25px;
            border-top:1px solid #eee;
        }

        .roles-info p {
            font-size:12px;
            color:#aaa;
            text-align:center;
            margin-bottom:12px;
            text-transform:uppercase;
            letter-spacing:1px;
        }

        .roles {
            display:flex;
            gap:10px;
            justify-content:center;
        }

        .role-badge {
            padding:6px 14px;
            border-radius:20px;
            font-size:12px;
            font-weight:600;
        }

        .role-badge.admin   { background:#e8eaf6; color:#3949ab; }
        .role-badge.teacher { background:#e8f5e9; color:#2e7d32; }
        .role-badge.parent  { background:#f3e5f5; color:#6a1b9a; }

        /* Responsive */
        @media(max-width:768px){
            .left-panel { display:none; }
            .right-panel { width:100%; }
        }
    </style>
<link rel="stylesheet" href="css/darkmode.css">
</head>
<body>
<button class="dark-toggle" id="darkToggle" onclick="toggleDarkMode()">🌙</button>
<script src="js/darkmode.js"></script>

<!-- Animated Background -->
<div class="bg-animation">
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>
</div>

<!-- Left Panel -->
<div class="left-panel">
    <div class="school-icon">🎓</div>
    <h1>Smart <span>Attendance</span> Management System</h1>
    <p>A complete solution for tracking student attendance. 
    Teachers, parents and admins all in one place!</p>

    <div class="features">
        <div class="feature-item">
            <span class="icon">👨‍💼</span>
            <div>
                <strong>Admin Panel</strong><br>
                <small style="opacity:0.7">Manage classes, teachers & students</small>
            </div>
        </div>
        <div class="feature-item">
            <span class="icon">👨‍🏫</span>
            <div>
                <strong>Teacher Panel</strong><br>
                <small style="opacity:0.7">Take & view attendance reports</small>
            </div>
        </div>
        <div class="feature-item">
            <span class="icon">👨‍👩‍👦</span>
            <div>
                <strong>Parent Panel</strong><br>
                <small style="opacity:0.7">Track your child's attendance weekly</small>
            </div>
        </div>
    </div>
</div>

<!-- Right Panel - Login Form -->
<div class="right-panel">
    <h2 class="login-title">Welcome Back! 👋</h2>
    <p class="login-subtitle">Please login to your account to continue</p>

    <?php if($error != ""): ?>
    <div class="error-box">
        ❌ <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="input-group">
            <label>Email Address</label>
            <div class="input-wrapper">
                <span class="input-icon">📧</span>
                <input type="email" name="email"
                placeholder="Enter your email" required>
            </div>
        </div>

        <div class="input-group">
            <label>Password</label>
            <div class="input-wrapper">
                <span class="input-icon">🔒</span>
                <input type="password" name="password"
                placeholder="Enter your password" required>
            </div>
        </div>

        <button type="submit" class="login-btn">
            Login to Dashboard →
        </button>
    </form>

    <div class="roles-info">
        <p>Available Roles</p>
        <div class="roles">
            <span class="role-badge admin">👨‍💼 Admin</span>
            <span class="role-badge teacher">👨‍🏫 Teacher</span>
            <span class="role-badge parent">👨‍👩‍👦 Parent</span>
        </div>
    </div>
</div>

</body>
</html>
