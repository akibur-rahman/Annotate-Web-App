<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "annotate";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $submitted_username = $_POST['username'];
    $submitted_password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $submitted_username);
    $stmt->execute();
    $stmt->bind_result($admin_id, $admin_password);
    $stmt->fetch();
    $stmt->close();

    if ($admin_id && $submitted_password === $admin_password) {
        $_SESSION['admin_id'] = $admin_id;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@material/button@14.0.0/dist/mdc.button.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-card {
            background-color: white;
            padding: 32px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        .login-card h2 {
            margin: 0 0 16px;
        }

        .login-card form {
            display: flex;
            flex-direction: column;
        }

        .login-card form input {
            margin-bottom: 16px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .login-card form button {
            padding: 8px;
            border: none;
            border-radius: 4px;
            background-color: #1976D2;
            color: white;
            cursor: pointer;
        }

        .login-card form button:hover {
            background-color: #1565C0;
        }

        .error {
            color: red;
            margin-top: 16px;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <h2>Admin Login</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="mdc-button mdc-button--raised">Login</button>
        </form>
        <?php if (isset($error)) : ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
    </div>
</body>

</html>