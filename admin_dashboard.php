<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "annotate";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get users and their annotation counts
$get_users_query = "
    SELECT users.id, users.first_name, users.last_name, COUNT(annotations.id) AS annotation_count
    FROM users
    LEFT JOIN annotations ON users.id = annotations.user_id
    GROUP BY users.id
    ORDER BY annotation_count DESC
";
$users_result = $conn->query($get_users_query);

$users = [];
if ($users_result->num_rows > 0) {
    while ($row = $users_result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            display: flex;
            flex-direction: column;
            height: 100vh;
            margin: 0;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(45deg, #1976D2, #64B5F6);
            color: white;
            padding: 16px 24px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logout-button {
            margin-left: 16px;
            color: white;
            background-color: transparent;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .logout-button:hover {
            text-decoration: underline;
        }

        main {
            flex: 1;
            padding: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        table th,
        table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #f0f0f0;
        }

        footer {
            background: linear-gradient(45deg, #1976D2, #64B5F6);
            color: white;
            text-align: center;
            padding: 16px;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
        }
    </style>
</head>

<body>
    <header>
        <div>Admin Dashboard</div>
        <button class="logout-button" onclick="location.href='logout.php'">Logout</button>
    </header>

    <main>
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Annotations</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['annotation_count']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <footer>
        © 2024 Your Company
    </footer>
</body>

</html>