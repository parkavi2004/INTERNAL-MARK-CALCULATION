<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $fullname = $_POST['fullname'];
    $department = $_POST['department'];
    $position = $_POST['position'];
    $subject = $role == 'staff' ? $_POST['subject'] : null;

    $sql = "INSERT INTO users (username, password, fullname, department, position, subject, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $username, $password, $fullname, $department, $position, $subject, $role);
    
    if ($stmt->execute()) {
        echo "New $role added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$sql = "SELECT * FROM users";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
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
        input[type="number"],
        input[type="password"]{
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
      
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin</h2>
        <h3>Add New Staff or Student</h3>
        <form action="admin_dashboard.php" method="post">
            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="fullname">Full Name:</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>
            <div class="form-group">
                <label for="department">Department:</label>
                <input type="text" id="department" name="department" required>
            </div>
            <div class="form-group">
                <label for="position">Position:</label>
                <input type="text" id="position" name="position" required>
            </div>
            <div class="form-group" id="subject-group" style="display: none;">
                <label for="subject">Subject:</label>
                <input type="text" id="subject" name="subject">
            </div>
            <div class="form-group">
                <button type="submit">Add User</button>
            </div>
        </form>

        <h3>Existing Users</h3>
        <table>
            <tr>
                <th>Username</th>
                <th>Full Name</th>
                <th>Department</th>
                <th>Position</th>
                <th>Subject</th>
                <th>Role</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['fullname']; ?></td>
                    <td><?php echo $row['department']; ?></td>
                    <td><?php echo $row['position']; ?></td>
                    <td><?php echo $row['subject']; ?></td>
                    <td><?php echo $row['role']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <script>
        document.getElementById('role').addEventListener('change', function() {
            var subjectGroup = document.getElementById('subject-group');
            if (this.value === 'staff') {
                subjectGroup.style.display = 'block';
            } else {
                subjectGroup.style.display = 'none';
            }
        });
    </script>
</body>
<a href="feedback.php">Feedback</a><br>
<a href="admin_logout.php">Logout</a>
</html>
