<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin'){
    header("Location: ../index.php");
    exit();
}

$success = "";
$error   = "";

$classes = mysqli_query($conn, "SELECT * FROM classes");
$parents = mysqli_query($conn, "SELECT * FROM users WHERE role='parent'");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name      = $_POST['name'];
    $roll_no   = $_POST['roll_no'];
    $class_id  = $_POST['class_id'];
    $parent_id = $_POST['parent_id'];

    $check = mysqli_query($conn,
    "SELECT * FROM students 
     WHERE roll_no='$roll_no' 
     AND class_id='$class_id'");

    if(mysqli_num_rows($check) > 0){
        $error = "Roll number already exists!";
    } else {
        $query = "INSERT INTO students (name, roll_no, class_id, parent_id)
                  VALUES ('$name','$roll_no','$class_id','$parent_id')";
        if(mysqli_query($conn, $query)){
            $success = "Student added successfully!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
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
td { padding:10px; border-bottom:1px solid #ddd; text-align:center; }

/* Action buttons column */
td:last-child { white-space:nowrap; }

/* Table row hover effect */
tbody tr { transition:all 0.2s; }
tbody tr:hover {
    background:#f0f4ff;
    transform:scale(1.01);
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

/* Search box style */
#searchBox { font-family:'Poppins',sans-serif; transition:all 0.3s; }
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
    <h2>👨‍🎓 Add New Student</h2>

    <?php if($success != ""): ?>
        <div class="success">✅ <?php echo $success; ?></div>
    <?php endif; ?>

    <?php if($error != ""): ?>
        <div class="error">❌ <?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>Student Name</label>
            <input type="text" name="name"
            placeholder="Enter student name" required>
        </div>

        <div class="input-group">
            <label>Roll Number</label>
            <input type="text" name="roll_no"
            placeholder="e.g. 101" required>
        </div>

        <div class="input-group">
            <label>Select Class</label>
            <select name="class_id" required>
                <option value="">-- Select Class --</option>
                <?php while($c = mysqli_fetch_assoc($classes)): ?>
                <option value="<?php echo $c['id']; ?>">
                    <?php echo $c['class_name']; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="input-group">
            <label>Select Parent</label>
            <select name="parent_id" required>
                <option value="">-- Select Parent --</option>
                <?php while($p = mysqli_fetch_assoc($parents)): ?>
                <option value="<?php echo $p['id']; ?>">
                    <?php echo $p['name']; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit">➕ Add Student</button>
    </form>
</div>

<!-- Show all students -->
<?php
$students = mysqli_query($conn,
    "SELECT students.*, classes.class_name,
     users.name as parent_name
     FROM students
     LEFT JOIN classes ON students.class_id = classes.id
     LEFT JOIN users ON students.parent_id = users.id
     ORDER BY students.id DESC");
?>
<div class="table-container">
    <h3 style="margin-bottom:15px; color:#1e3c72;">
        📋 All Students
    </h3>

    <!-- Search Box -->
    <div style="margin-bottom:15px;">
        <input type="text" id="searchBox"
        onkeyup="searchStudents()"
        placeholder="🔍 Search by name or roll number..."
        style="width:100%; padding:10px 15px;
        border:2px solid #ddd; border-radius:8px;
        font-size:14px; font-family:'Poppins',sans-serif;">
    </div>

    <table id="studentTable">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Roll No</th>
            <th>Class</th>
            <th>Parent</th>
            <th>Actions</th>
        </tr>
        <?php
        $i = 1;
        while($s = mysqli_fetch_assoc($students)): ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo $s['name']; ?></td>
            <td><?php echo $s['roll_no']; ?></td>
            <td><?php echo $s['class_name']; ?></td>
            <td><?php echo $s['parent_name']; ?></td>
            <td>
                <a href="edit_student.php?id=<?php echo $s['id']; ?>"
style="
    display:inline-flex;
    align-items:center;
    gap:5px;
    background:linear-gradient(135deg, #1e3c72, #2a5298);
    color:white;
    padding:7px 15px;
    border-radius:20px;
    text-decoration:none;
    font-size:12px;
    font-weight:600;
    margin-right:5px;
    box-shadow:0 3px 10px rgba(30,60,114,0.3);
    transition:all 0.3s;
    font-family:'Poppins',sans-serif;"
onmouseover="this.style.transform='translateY(-2px)';
             this.style.boxShadow='0 5px 15px rgba(30,60,114,0.4)'"
onmouseout="this.style.transform='translateY(0)';
            this.style.boxShadow='0 3px 10px rgba(30,60,114,0.3)'">
    ✏️ Edit
</a>
<a href="delete_student.php?id=<?php echo $s['id']; ?>"
onclick="return confirm('⚠️ Are you sure?\n\nThis will permanently delete this student and all their attendance records!')"
style="
    display:inline-flex;
    align-items:center;
    gap:5px;
    background:linear-gradient(135deg, #f44336, #c62828);
    color:white;
    padding:7px 15px;
    border-radius:20px;
    text-decoration:none;
    font-size:12px;
    font-weight:600;
    box-shadow:0 3px 10px rgba(244,67,54,0.3);
    transition:all 0.3s;
    font-family:'Poppins',sans-serif;"
onmouseover="this.style.transform='translateY(-2px)';
             this.style.boxShadow='0 5px 15px rgba(244,67,54,0.4)'"
onmouseout="this.style.transform='translateY(0)';
            this.style.boxShadow='0 3px 10px rgba(244,67,54,0.3)'">
    🗑️ Delete
</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
function searchStudents(){
    var input = document.getElementById('searchBox').value.toLowerCase();
    var table = document.getElementById('studentTable');
    var rows  = table.getElementsByTagName('tr');

    for(var i=1; i<rows.length; i++){
        var name   = rows[i].getElementsByTagName('td')[1];
        var rollno = rows[i].getElementsByTagName('td')[2];
        if(name || rollno){
            var nameText   = name.textContent.toLowerCase();
            var rollnoText = rollno.textContent.toLowerCase();
            if(nameText.includes(input) || rollnoText.includes(input)){
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
}
</script>