<?php
session_start();
if ($_SESSION['role'] !== 'student') {
    header("Location: index.html");
    exit();
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_username = $_SESSION['username'];

// Fetch student details for the logged-in student
$sql_student_details = "SELECT * FROM users WHERE username = ?";
$stmt_student_details = $conn->prepare($sql_student_details);
$stmt_student_details->bind_param("s", $student_username);
$stmt_student_details->execute();
$result_student_details = $stmt_student_details->get_result();

// Fetch marks for the logged-in student
$sql_marks = "SELECT * FROM marks WHERE student_username = ?";
$stmt_marks = $conn->prepare($sql_marks);
$stmt_marks->bind_param("s", $student_username);
$stmt_marks->execute();
$result_marks = $stmt_marks->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff; 
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            margin: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 80%;
            max-width: 900px;
        }
        h2, h3 {
            margin-top: 0;
            color: #333;
        }
        .student-details div {
            margin-bottom: 15px;
        }
        .student-details hr {
            margin: 15px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        a {
            display: inline-block;
            margin: 10px 0;
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome!!!</h2>
        
        <h3>Your Details</h3>
        <div class="student-details">
            <?php if ($row_student_details = $result_student_details->fetch_assoc()): ?>
                <div>
                    <p><strong>Username:</strong> <?php echo $row_student_details['username']; ?></p>
                    <p><strong>Name:</strong> <?php echo $row_student_details['fullname']; ?></p>
                    <p><strong>Department:</strong> <?php echo $row_student_details['department']; ?></p>
                    <p><strong>Position:</strong> <?php echo $row_student_details['position']; ?></p>
                </div>
            <?php else: ?>
                <p>No details found for the student.</p>
            <?php endif; ?>
        </div>
        
        <h3>Marks</h3>
        <table>
            <tr>
                <th>Subject</th>
                <th>CA Test</th>
                <th>Model Lab</th>
                <th>Model Test</th>
                <th>Sem Lab</th>
                <th>Total Internal</th>
                <th>Staff Name</th>
            </tr>
            <?php while($row_marks = $result_marks->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row_marks['subject']; ?></td>
                    <td><?php echo $row_marks['caTest']; ?></td>
                    <td><?php echo $row_marks['modelLab']; ?></td>
                    <td><?php echo $row_marks['modelTest']; ?></td>
                    <td><?php echo $row_marks['semLab']; ?></td>
                    <td><?php echo $row_marks['totalInternal']; ?></td>
                    <td><?php echo $row_marks['staff_username']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        
    </div>
    <a href="feedback.php">Feedback</a><br>
    <a href="student_logout.php">Logout</a>
</body>
</html>
