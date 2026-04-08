<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['user_id']) ||
   $_SESSION['user_role'] != 'teacher'){
    header("Location: ../index.php");
    exit();
}

$class_id      = $_GET['class_id'];
$selected_date = $_GET['date'];
$teacher_name  = $_SESSION['user_name'];

// Get class info
$class = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM classes WHERE id=$class_id"));

// Get attendance data
$attendance = mysqli_query($conn,
    "SELECT attendance.*, students.name, students.roll_no
     FROM attendance
     LEFT JOIN students ON attendance.student_id = students.id
     WHERE attendance.class_id=$class_id
     AND attendance.date='$selected_date'
     ORDER BY students.roll_no ASC");

// Count summary
$present = 0; $absent = 0; $late = 0;
$temp = mysqli_query($conn,
    "SELECT status, COUNT(*) as count
     FROM attendance
     WHERE class_id=$class_id
     AND date='$selected_date'
     GROUP BY status");
while($t = mysqli_fetch_assoc($temp)){
    if($t['status'] == 'present') $present = $t['count'];
    if($t['status'] == 'absent')  $absent  = $t['count'];
    if($t['status'] == 'late')    $late    = $t['count'];
}
$total = $present + $absent + $late;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Report - <?php echo $selected_date; ?></title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: Arial, sans-serif;
            background:white;
            padding:30px;
            color:#333;
        }

        /* Header */
        .header {
            text-align:center;
            padding-bottom:20px;
            border-bottom:3px solid #1e3c72;
            margin-bottom:25px;
        }

        .header .school-name {
            font-size:24px;
            font-weight:bold;
            color:#1e3c72;
            margin-bottom:5px;
        }

        .header .report-title {
            font-size:16px;
            color:#555;
            margin-bottom:5px;
        }

        .header .report-date {
            font-size:13px;
            color:#888;
        }

        /* Info Section */
        .info-section {
            display:flex;
            justify-content:space-between;
            margin-bottom:20px;
            background:#f8f9ff;
            padding:15px 20px;
            border-radius:8px;
            border-left:4px solid #1e3c72;
        }

        .info-item { font-size:13px; }
        .info-item strong { color:#1e3c72; }

        /* Summary Boxes */
        .summary {
            display:flex;
            gap:15px;
            margin-bottom:25px;
        }

        .summary-box {
            flex:1;
            padding:15px;
            border-radius:8px;
            text-align:center;
            color:white;
        }

        .summary-box h3 { font-size:28px; font-weight:bold; }
        .summary-box p  { font-size:12px; margin-top:3px; }

        .s-green  { background:#2e7d32; }
        .s-red    { background:#c62828; }
        .s-orange { background:#e65100; }
        .s-blue   { background:#1e3c72; }

        /* Table */
        table {
            width:100%;
            border-collapse:collapse;
            margin-bottom:25px;
        }

        thead tr { background:#1e3c72; }

        thead th {
            padding:12px 15px;
            color:white;
            font-size:13px;
            text-align:left;
        }

        tbody tr:nth-child(even){ background:#f8f9ff; }
        tbody tr:nth-child(odd) { background:white; }

        tbody td {
            padding:11px 15px;
            font-size:13px;
            border-bottom:1px solid #eee;
        }

        .status-present {
            color:#2e7d32;
            font-weight:bold;
            background:#e8f5e9;
            padding:3px 10px;
            border-radius:12px;
            font-size:12px;
        }

        .status-absent {
            color:#c62828;
            font-weight:bold;
            background:#ffebee;
            padding:3px 10px;
            border-radius:12px;
            font-size:12px;
        }

        .status-late {
            color:#e65100;
            font-weight:bold;
            background:#fff3e0;
            padding:3px 10px;
            border-radius:12px;
            font-size:12px;
        }

        /* No data */
        .no-data {
            text-align:center;
            padding:40px;
            color:#888;
            font-size:16px;
        }

        /* Footer */
        .footer {
            margin-top:30px;
            padding-top:20px;
            border-top:2px solid #eee;
            display:flex;
            justify-content:space-between;
            font-size:12px;
            color:#888;
        }

        .signature {
            margin-top:50px;
            display:flex;
            justify-content:space-between;
        }

        .signature-box {
            text-align:center;
            width:180px;
        }

        .signature-line {
            border-top:1px solid #333;
            margin-bottom:8px;
        }

        .signature-box p {
            font-size:12px;
            color:#555;
        }

        /* Print Button */
        .print-btn {
            position:fixed;
            bottom:30px;
            right:30px;
            background:linear-gradient(135deg, #1e3c72, #2a5298);
            color:white;
            padding:15px 25px;
            border:none;
            border-radius:50px;
            font-size:15px;
            font-weight:bold;
            cursor:pointer;
            box-shadow:0 5px 20px rgba(30,60,114,0.4);
            z-index:999;
            font-family:Arial, sans-serif;
            display:flex;
            align-items:center;
            gap:8px;
            transition:all 0.3s;
        }

        .print-btn:hover {
            transform:translateY(-3px);
            box-shadow:0 8px 25px rgba(30,60,114,0.5);
        }

        /* Hide print button when printing */
        @media print {
            .print-btn { display:none !important; }
            body { padding:15px; }
        }
    </style>
</head>
<body>

<!-- Print Button -->
<button class="print-btn" onclick="window.print()">
    🖨️ Print / Save PDF
</button>

<!-- Header -->
<div class="header">
    <div class="school-name">🎓 Student Attendance System</div>
    <div class="report-title">Daily Attendance Report</div>
    <div class="report-date">
        Generated on: <?php echo date('d F Y, h:i A'); ?>
    </div>
</div>

<!-- Info Section -->
<div class="info-section">
    <div class="info-item">
        📅 <strong>Date:</strong>
        <?php echo date('d F Y', strtotime($selected_date)); ?>
    </div>
    <div class="info-item">
        🏫 <strong>Class:</strong>
        <?php echo $class['class_name']; ?>
    </div>
    <div class="info-item">
        👨‍🏫 <strong>Teacher:</strong>
        <?php echo $teacher_name; ?>
    </div>
    <div class="info-item">
        👨‍🎓 <strong>Total Students:</strong>
        <?php echo $total; ?>
    </div>
</div>

<!-- Summary -->
<div class="summary">
    <div class="summary-box s-green">
        <h3><?php echo $present; ?></h3>
        <p>Present</p>
    </div>
    <div class="summary-box s-red">
        <h3><?php echo $absent; ?></h3>
        <p>Absent</p>
    </div>
    <div class="summary-box s-orange">
        <h3><?php echo $late; ?></h3>
        <p>Late</p>
    </div>
    <div class="summary-box s-blue">
        <h3>
            <?php echo $total > 0 ?
                round(($present/$total)*100) : 0; ?>%
        </h3>
        <p>Attendance Rate</p>
    </div>
</div>

<!-- Attendance Table -->
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Roll No</th>
            <th>Student Name</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $i = 1;
    $found = false;
    while($a = mysqli_fetch_assoc($attendance)){
        $found = true;
    ?>
    <tr>
        <td><?php echo $i++; ?></td>
        <td><?php echo $a['roll_no']; ?></td>
        <td><?php echo $a['name']; ?></td>
        <td>
            <span class="status-<?php echo $a['status']; ?>">
                <?php echo strtoupper($a['status']); ?>
            </span>
        </td>
    </tr>
    <?php } ?>

    <?php if(!$found){ ?>
    <tr>
        <td colspan="4">
            <div class="no-data">
                📭 No attendance records found for this date!
            </div>
        </td>
    </tr>
    <?php } ?>
    </tbody>
</table>

<!-- Signature Section -->
<div class="signature">
    <div class="signature-box">
        <div class="signature-line"></div>
        <p>Class Teacher</p>
        <p><strong><?php echo $teacher_name; ?></strong></p>
    </div>
    <div class="signature-box">
        <div class="signature-line"></div>
        <p>Principal Signature</p>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <span>
        📄 Attendance Report -
        <?php echo $class['class_name']; ?> -
        <?php echo date('d M Y', strtotime($selected_date)); ?>
    </span>
    <span>
        Student Attendance Management System
    </span>
</div>

</body>
</html>