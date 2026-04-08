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
    $role     = 'teacher';

    $check = mysqli_query($conn,
        "SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($check) > 0){
        $error = "Email already exists!";
    } else {
        $query = "INSERT INTO users (name, email, password, role)
                  VALUES ('$name','$email','$password','$role')";
        if(mysqli_query($conn, $query)){
            $success = "Teacher added successfully!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Teacher</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Poppins', sans-serif;
            background:#f0f2f5;
        }
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
            font-family:'Poppins', sans-serif;
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
            font-family:'Poppins', sans-serif;
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
            width:700px;
            margin:20px auto;
            background:white;
            padding:20px;
            border-radius:10px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }
        table { width:100%; border-collapse:collapse; }
        th { background:#1e3c72; color:white; padding:12px; text-align:left; }
        td { padding:12px; border-bottom:1px solid #ddd; }
        td:last-child { white-space:nowrap; }
        tbody tr { transition:all 0.2s; }
        tbody tr:hover { background:#f0f4ff; }
        #searchBox { font-family:'Poppins', sans-serif; }
        #searchBox:focus {
            border-color:#1e3c72 !important;
            outline:none;
            box-shadow:0 0 0 3px rgba(30,60,114,0.1);
        }
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
    <h2>👨‍🏫 Add New Teacher</h2>

    <?php if($success != ""){ ?>
        <div class="success">✅ <?php echo $success; ?></div>
    <?php } ?>

    <?php if($error != ""){ ?>
        <div class="error">❌ <?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <div class="input-group">
            <label>Teacher Name</label>
            <input type="text" name="name"
            placeholder="Enter teacher name" required>
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

        <button type="submit">➕ Add Teacher</button>
    </form>
</div>

<!-- Show all teachers -->
<?php
$teachers = mysqli_query($conn,
    "SELECT * FROM users WHERE role='teacher' ORDER BY id DESC");
?>
<div class="table-container">
    <h3 style="margin-bottom:15px; color:#1e3c72;">
        📋 All Teachers
    </h3>

    <div style="margin-bottom:15px;">
        <input type="text" id="searchBox"
        onkeyup="searchTeachers()"
        placeholder="🔍 Search by name or email..."
        style="width:100%; padding:10px 15px;
        border:2px solid #ddd; border-radius:8px;
        font-size:14px;">
    </div>

    <table id="teacherTable">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php
        $i = 1;
        while($t = mysqli_fetch_assoc($teachers)){
        ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo $t['name']; ?></td>
            <td><?php echo $t['email']; ?></td>
            <td>
                <a href="edit_teacher.php?id=<?php echo $t['id']; ?>"
                style="
                    display:inline-flex;
                    align-items:center;
                    background:linear-gradient(135deg,#1e3c72,#2a5298);
                    color:white;
                    padding:7px 15px;
                    border-radius:20px;
                    text-decoration:none;
                    font-size:12px;
                    font-weight:600;
                    margin-right:5px;
                    box-shadow:0 3px 10px rgba(30,60,114,0.3);"
                onmouseover="this.style.transform='translateY(-2px)'"
                onmouseout="this.style.transform='translateY(0)'">
                    ✏️ Edit
                </a>
                <a href="delete_teacher.php?id=<?php echo $t['id']; ?>"
                onclick="return confirm('⚠️ Are you sure?\n\nThis will delete this teacher!')"
                style="
                    display:inline-flex;
                    align-items:center;
                    background:linear-gradient(135deg,#f44336,#c62828);
                    color:white;
                    padding:7px 15px;
                    border-radius:20px;
                    text-decoration:none;
                    font-size:12px;
                    font-weight:600;
                    box-shadow:0 3px 10px rgba(244,67,54,0.3);"
                onmouseover="this.style.transform='translateY(-2px)'"
                onmouseout="this.style.transform='translateY(0)'">
                    🗑️ Delete
                </a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<script>
function searchTeachers(){
    var input = document.getElementById('searchBox').value.toLowerCase();
    var table = document.getElementById('teacherTable');
    var rows  = table.getElementsByTagName('tr');
    for(var i=1; i<rows.length; i++){
        var name  = rows[i].getElementsByTagName('td')[1];
        var email = rows[i].getElementsByTagName('td')[2];
        if(name || email){
            var nameText  = name.textContent.toLowerCase();
            var emailText = email.textContent.toLowerCase();
            if(nameText.includes(input) || emailText.includes(input)){
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
}
</script>

</body>
</html>