<?php
session_start();

if ($_SESSION['role'] !== 'staff') {
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

$resultMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['assign_subject'])) {
        $student_username = $_POST['student_username'];
        $subject = $_POST['subject'];

        $sql = "INSERT INTO student_subjects (student_username, subject) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $student_username, $subject);

        if ($stmt->execute()) {
            $resultMessage = "Subject assigned successfully.";
        } else {
            $resultMessage = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $student_username = $_POST['student_username'];
        $staff_username = $_SESSION['username'];
        $subject = $_POST['subject'];
        $caTest = $_POST['caTest'];
        $modelLab = $_POST['modelLab'];
        $modelTest = $_POST['modelTest'];
        $semLab = $_POST['semLab'];
        $totalInternal = $_POST['totalInternal'];

        $sql = "INSERT INTO marks (student_username, staff_username, subject, caTest, modelLab, modelTest, semLab, totalInternal) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssdddd", $student_username, $staff_username, $subject, $caTest, $modelLab, $modelTest, $semLab, $totalInternal);

        if ($stmt->execute()) {
            $resultMessage = "Marks added successfully.";
        } else {
            $resultMessage = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$staff_username = $_SESSION['username'];
$sql = "SELECT * FROM marks WHERE staff_username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $staff_username);
$stmt->execute();
$result = $stmt->get_result();

$sql_subjects = "SELECT * FROM student_subjects WHERE student_username IN (SELECT student_username FROM marks WHERE staff_username = ?)";
$stmt_subjects = $conn->prepare($sql_subjects);
$stmt_subjects->bind_param("s", $staff_username);
$stmt_subjects->execute();
$result_subjects = $stmt_subjects->get_result();

$sql_staff = "SELECT * FROM users WHERE username = ?";
$stmt_staff = $conn->prepare($sql_staff);
$stmt_staff->bind_param("s", $staff_username);
$stmt_staff->execute();
$result_staff = $stmt_staff->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa; 
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
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            margin: 10px 0;
            border: none;
            cursor: pointer;
            width: 100%;
            border-radius: 4px;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
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
        .staff-details div {
            margin-bottom: 15px;
        }
        .staff-details hr {
            margin: 15px 0;
        }
    </style>
    <script>
        function calculateInternal() {
            var caTest = parseFloat(document.getElementById('caTest').value) || 0;
            var modelLab = parseFloat(document.getElementById('modelLab').value) || 0;
            var modelTest = parseFloat(document.getElementById('modelTest').value) || 0;
            var semLab = parseFloat(document.getElementById('semLab').value) || 0;

            var caTestInternal = (caTest * 10) / 60;
            var modelLabInternal = (modelLab * 10) / 50;
            var modelTestInternal = (modelTest * 10) / 100;
            var semLabInternal = (semLab * 10) / 100;

            var totalInternal = caTestInternal + modelLabInternal + modelTestInternal + semLabInternal;
            document.getElementById('totalInternal').value = totalInternal.toFixed(2);
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Staff</h2>
        <h3>Staff Details</h3>
        <div class="staff-details">
            <?php if($row_staff = $result_staff->fetch_assoc()): ?>
                <div>
                    <p><strong>Username:</strong> <?php echo $row_staff['username']; ?></p>
                    <p><strong>Name:</strong> <?php echo $row_staff['fullname']; ?></p>
                    <p><strong>Department:</strong> <?php echo $row_staff['department']; ?></p>
                    <p><strong>Position:</strong> <?php echo $row_staff['position']; ?></p>
                <p><strong>Major Handling:</strong><?php echo $row_staff['subject'];?>
                </div>
            <?php endif; ?><hr>
        </div>
        <h3>Enter Student Marks</h3>
        <?php if (!empty($resultMessage)): ?>
            <p><?php echo $resultMessage; ?></p>
        <?php endif; ?>
        <form action="staff_dashboard.php" method="post">
            <div class="form-group">
                <label for="student_username">Student Username:</label>
                <input type="text" id="student_username" name="student_username" required>
            </div>
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="caTest">CA Test:</label>
                <input type="number" id="caTest" name="caTest" oninput="calculateInternal()" required>
            </div>
            <div class="form-group">
                <label for="modelLab">Model Lab:</label>
                <input type="number" id="modelLab" name="modelLab" oninput="calculateInternal()" required>
            </div>
            <div class="form-group">
                <label for="modelTest">Model Test:</label>
                <input type="number" id="modelTest" name="modelTest" oninput="calculateInternal()" required>
            </div>
            <div class="form-group">
                <label for="semLab">Sem Lab:</label>
                <input type="number" id="semLab" name="semLab" oninput="calculateInternal()" required>
            </div>
            <div class="form-group">
                <label for="totalInternal">Total Internal:</label>
                <input type="text" id="totalInternal" name="totalInternal" readonly>
            </div>
            <div class="form-group">
                <button type="submit">Submit Marks</button>
            </div>
        </form>

        <h3>Marks Entered</h3>
        <table>
            <tr>
                <th>Student Username</th>
                <th>Subject</th>
                <th>CA Test</th>
                <th>Model Lab</th>
                <th>Model Test</th>
                <th>Sem Lab</th>
                <th>Total Internal</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['student_username']; ?></td>
                    <td><?php echo $row['subject']; ?></td>
                    <td><?php echo $row['caTest']; ?></td>
                    <td><?php echo $row['modelLab']; ?></td>
                    <td><?php echo $row['modelTest']; ?></td>
                    <td><?php echo $row['semLab']; ?></td>
                    <td><?php echo $row['totalInternal']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

    </div>
</body>
<a href="feedback.php">Feedback</a><br>
<a href="student_logout.php">Logout</a>
</html>
