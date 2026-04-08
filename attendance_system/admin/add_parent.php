<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['user_id']) || 
   $_SESSION['user_role'] != 'admin'){
    header("Location: ../index.php");
    exit();
}

$success = "";
$error   = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = 'parent';

    $check = mysqli_query($conn,
        "SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($check) > 0){
        $error = "Email already exists!";
    } else {
        $query = "INSERT INTO users (name, email, password, role)
                  VALUES ('$name','$email','$password','$role')";
        if(mysqli_query($conn, $query)){
            $success = "Parent added successfully!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Parent</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box;
            font-family:Arial, sans-serif; }
        body { background:#f0f2f5; }
        .navbar {
            background:#1e3c72;
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
            width:500px;
            margin:40px auto;
            background:white;
            padding:30px;
            border-radius:10px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }
        .container h2 { color:#1e3c72; margin-bottom:20px; }
        .input-group { margin-bottom:20px; }
        .input-group label {
            display:block;
            margin-bottom:5px;
            color:#555;
        }
        .input-group input {
            width:100%;
            padding:10px;
            border:2px solid #ddd;
            border-radius:8px;
            font-size:15px;
        }
        .input-group input:focus {
            border-color:#1e3c72;
            outline:none;
        }
        button {
            width:100%;
            padding:12px;
            background:#1e3c72;
            color:white;
            border:none;
            border-radius:8px;
            font-size:16px;
            cursor:pointer;
        }
        button:hover { background:#16305e; }
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
        .table-container {
            width:500px;
            margin:20px auto;
            background:white;
            padding:20px;
            border-radius:10px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }
        table { width:100%; border-collapse:collapse; }
        th { background:#1e3c72; color:white; padding:10px; }
        td { padding:10px; border-bottom:1px solid #ddd;
             text-align:center; }
    </style>
<link rel="stylesheet" href="../css/darkmode.css">
</head>
<body>
<button class="dark-toggle" id="darkToggle" onclick="toggleDarkMode()">🌙</button>
<script src="../js/darkmode.js"></script>

 <div class="navbar">
    <h2>🎓 Admin Panel</h2>
    <div>
        <a href="javascript:history.back()">← Back</a>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>👨‍👩‍👦 Add New Parent</h2>

    <?php if($success != ""): ?>
        <div class="success">✅ <?php echo $success; ?></div>
    <?php endif; ?>

    <?php if($error != ""): ?>
        <div class="error">❌ <?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>Parent Name</label>
            <input type="text" name="name"
            placeholder="Enter parent name" required>
        </div>

        <div class="input-group">
            <label>Email Address</label>
            <input type="email" name="email"
            placeholder="Enter email" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password"
            placeholder="Enter password" required>
        </div>

        <button type="submit">➕ Add Parent</button>
    </form>
</div>

<!-- Show all parents -->
<?php
$parents = mysqli_query($conn,
    "SELECT * FROM users WHERE role='parent'");
?>
<div class="table-container">
    <h3 style="margin-bottom:15px; color:#1e3c72;">
        📋 All Parents
    </h3>
    <table>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
        <?php
        $i = 1;
        while($p = mysqli_fetch_assoc($parents)): ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo $p['name']; ?></td>
            <td><?php echo $p['email']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>