<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['user_id']) ||
   $_SESSION['user_role'] != 'admin'){
    header("Location: ../index.php");
    exit();
}

$id      = $_GET['id'];
$success = "";
$error   = "";

// Get teacher data
$teacher = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM users WHERE id=$id AND role='teacher'"));

// Update teacher
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name  = $_POST['name'];
    $email = $_POST['email'];

    // Check duplicate email
    $check = mysqli_query($conn,
        "SELECT * FROM users
         WHERE email='$email' AND id != $id");

    if(mysqli_num_rows($check) > 0){
        $error = "Email already exists!";
    } else {
        // Check if password is being changed
        if(!empty($_POST['password'])){
            $password = password_hash($_POST['password'],
                PASSWORD_DEFAULT);
            $query = "UPDATE users SET
                      name='$name',
                      email='$email',
                      password='$password'
                      WHERE id=$id";
        } else {
            $query = "UPDATE users SET
                      name='$name',
                      email='$email'
                      WHERE id=$id";
        }

        if(mysqli_query($conn, $query)){
            $success = "Teacher updated successfully!";
            $teacher = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT * FROM users WHERE id=$id"));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Teacher</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Poppins', sans-serif;
            background:#f0f2f5;
            min-height:100vh;
        }
        .navbar {
            background:linear-gradient(135deg, #1a1a2e, #16213e);
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
            padding:8px 18px;
            border-radius:8px;
            font-size:14px;
            margin-left:10px;
            background:rgba(255,255,255,0.1);
            transition:all 0.3s;
        }
        .navbar a:hover { background:rgba(255,255,255,0.2); }
        .container {
            width:550px;
            margin:40px auto;
            background:white;
            padding:35px;
            border-radius:15px;
            box-shadow:0 5px 20px rgba(0,0,0,0.1);
        }
        .container h2 {
            color:#1a1a2e;
            margin-bottom:25px;
            font-size:22px;
            padding-bottom:15px;
            border-bottom:2px solid #f0f0f0;
        }
        .input-group { margin-bottom:20px; }
        .input-group label {
            display:block;
            margin-bottom:8px;
            color:#444;
            font-size:13px;
            font-weight:600;
            text-transform:uppercase;
            letter-spacing:0.5px;
        }
        .input-group input {
            width:100%;
            padding:12px 15px;
            border:2px solid #eee;
            border-radius:10px;
            font-size:14px;
            font-family:'Poppins', sans-serif;
            transition:all 0.3s;
            background:#f8f9ff;
        }
        .input-group input:focus {
            border-color:#6c63ff;
            outline:none;
            background:white;
        }
        .hint {
            font-size:12px;
            color:#aaa;
            margin-top:5px;
        }
        .btn-group {
            display:flex;
            gap:15px;
            margin-top:10px;
        }
        .btn-save {
            flex:1;
            padding:13px;
            background:linear-gradient(135deg, #6c63ff, #3ecf8e);
            color:white;
            border:none;
            border-radius:10px;
            font-size:15px;
            font-weight:600;
            font-family:'Poppins', sans-serif;
            cursor:pointer;
            transition:all 0.3s;
        }
        .btn-save:hover {
            transform:translateY(-2px);
            box-shadow:0 8px 20px rgba(108,99,255,0.4);
        }
        .btn-cancel {
            flex:1;
            padding:13px;
            background:#f0f0f0;
            color:#444;
            border:none;
            border-radius:10px;
            font-size:15px;
            font-weight:600;
            font-family:'Poppins', sans-serif;
            cursor:pointer;
            text-decoration:none;
            text-align:center;
            transition:all 0.3s;
        }
        .btn-cancel:hover { background:#e0e0e0; }
        .success {
            background:#e8f5e9;
            color:#2e7d32;
            padding:12px 15px;
            border-radius:10px;
            margin-bottom:20px;
            border-left:4px solid #4caf50;
            font-size:14px;
        }
        .error {
            background:#ffebee;
            color:#c62828;
            padding:12px 15px;
            border-radius:10px;
            margin-bottom:20px;
            border-left:4px solid #f44336;
            font-size:14px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>🎓 Admin Panel</h2>
    <div>
        <a href="add_teacher.php">← Back</a>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>✏️ Edit Teacher</h2>

    <?php if($success != ""): ?>
        <div class="success">✅ <?php echo $success; ?></div>
    <?php endif; ?>

    <?php if($error != ""): ?>
        <div class="error">❌ <?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>Teacher Name</label>
            <input type="text" name="name"
            value="<?php echo $teacher['name']; ?>" required>
        </div>

        <div class="input-group">
            <label>Email Address</label>
            <input type="email" name="email"
            value="<?php echo $teacher['email']; ?>" required>
        </div>

        <div class="input-group">
            <label>New Password</label>
            <input type="password" name="password"
            placeholder="Leave blank to keep same password">
            <p class="hint">
                💡 Only fill this if you want to change password
            </p>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn-save">
                💾 Save Changes
            </button>
            <a href="add_teacher.php" class="btn-cancel">
                ✖ Cancel
            </a>
        </div>
    </form>
</div>

</body>
</html>