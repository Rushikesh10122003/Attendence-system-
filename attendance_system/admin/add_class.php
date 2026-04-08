<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['user_id']) || 
   $_SESSION['user_role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$success = "";
$error = "";

// Get all teachers for dropdown
$teachers = mysqli_query($conn, 
    "SELECT * FROM users WHERE role='teacher'");

// Save class when form submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_name = $_POST['class_name'];
    $teacher_id = $_POST['teacher_id'];

    $check = mysqli_query($conn, 
        "SELECT * FROM classes WHERE class_name='$class_name'");
    
    if(mysqli_num_rows($check) > 0) {
        $error = "Class already exists!";
    } else {
        $query = "INSERT INTO classes (class_name, teacher_id) 
                  VALUES ('$class_name', '$teacher_id')";
        if(mysqli_query($conn, $query)) {
            $success = "Class added successfully!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Class</title>
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
        .container h2 {
            color:#1e3c72;
            margin-bottom:20px;
        }
        .input-group {
            margin-bottom:20px;
        }
        .input-group label {
            display:block;
            margin-bottom:5px;
            color:#555;
        }
        .input-group input,
        .input-group select {
            width:100%;
            padding:10px;
            border:2px solid #ddd;
            border-radius:8px;
            font-size:15px;
        }
        .input-group input:focus,
        .input-group select:focus {
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

        /* Class List Table */
        .table-container {
            width:500px;
            margin:20px auto;
            background:white;
            padding:20px;
            border-radius:10px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }
        table {
            width:100%;
            border-collapse:collapse;
        }
        th {
            background:#1e3c72;
            color:white;
            padding:10px;
        }
        td {
            padding:10px;
            border-bottom:1px solid #ddd;
            text-align:center;
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
    <h2>🏫 Add New Class</h2>

    <?php if($success != ""): ?>
        <div class="success">✅ <?php echo $success; ?></div>
    <?php endif; ?>

    <?php if($error != ""): ?>
        <div class="error">❌ <?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>Class Name</label>
            <input type="text" name="class_name" 
            placeholder="e.g. Class 10A" required>
        </div>

        <div class="input-group">
            <label>Assign Teacher</label>
            <select name="teacher_id" required>
                <option value="">-- Select Teacher --</option>
                <?php while($t = mysqli_fetch_assoc($teachers)): ?>
                <option value="<?php echo $t['id']; ?>">
                    <?php echo $t['name']; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit">➕ Add Class</button>
    </form>
</div>

<!-- Show existing classes -->
<!-- Show all classes -->
<?php
$classes = mysqli_query($conn,
    "SELECT classes.*, users.name as teacher_name
     FROM classes
     LEFT JOIN users ON classes.teacher_id = users.id
     ORDER BY classes.id DESC");
?>
<div class="table-container">
    <h3 style="margin-bottom:15px; color:#1e3c72;">
        📋 All Classes
    </h3>

    <div style="margin-bottom:15px;">
        <input type="text" id="searchBox"
        onkeyup="searchClasses()"
        placeholder="🔍 Search by class name or teacher..."
        style="width:100%; padding:10px 15px;
        border:2px solid #ddd; border-radius:8px;
        font-size:14px; font-family:'Poppins',sans-serif;">
    </div>

    <table id="classTable">
        <tr>
            <th>#</th>
            <th>Class Name</th>
            <th>Teacher</th>
            <th>Actions</th>
        </tr>
        <?php
        $i = 1;
        while($c = mysqli_fetch_assoc($classes)){
        ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo $c['class_name']; ?></td>
            <td><?php echo $c['teacher_name']; ?></td>
            <td>
                <a href="edit_class.php?id=<?php echo $c['id']; ?>"
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
                <a href="delete_class.php?id=<?php echo $c['id']; ?>"
                onclick="return confirm('⚠️ Are you sure?\n\nThis will delete this class and all its attendance records!')"
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
function searchClasses(){
    var input = document.getElementById('searchBox').value.toLowerCase();
    var table = document.getElementById('classTable');
    var rows  = table.getElementsByTagName('tr');
    for(var i=1; i<rows.length; i++){
        var name    = rows[i].getElementsByTagName('td')[1];
        var teacher = rows[i].getElementsByTagName('td')[2];
        if(name || teacher){
            var nameText    = name.textContent.toLowerCase();
            var teacherText = teacher.textContent.toLowerCase();
            if(nameText.includes(input) || teacherText.includes(input)){
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
}
</script>