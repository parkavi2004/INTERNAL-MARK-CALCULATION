<?php
session_start();

$admin_username = "admin";
$admin_password = "admin123";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$role = $_POST['role'];
$user = $_POST['username'];
$pass = $_POST['password'];

if ($role == "admin") {
    if ($user == $admin_username && $pass == $admin_password) {
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = $user;
        header("Location: admin_dashboard.php");
    } else {
        echo "Invalid admin credentials.";
    }
} else {
    $sql = "SELECT * FROM users WHERE username = ? AND password = ? AND role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $user, $pass, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['role'] = $role;
        $_SESSION['username'] = $user;
        if ($role == "student") {
            header("Location: student_dashboard.php");
        } else if ($role == "staff") {
            header("Location: staff_dashboard.php");
        }
    } else {
        echo "Invalid credentials.";
    }

    $stmt->close();
}

$conn->close();
?>
