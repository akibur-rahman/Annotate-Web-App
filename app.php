<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "annotate";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user information
$user_id = $_SESSION['user_id'];
$get_user_query = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($get_user_query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $first_name = $user['first_name'];
    $last_name = $user['last_name'];
} else {
    // Redirect to login page if user not found
    header("Location: login.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        /* Resetting default margin and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #1976D2;
            color: white;
            padding: 10px 20px;
        }

        .profile {
            display: flex;
            align-items: center;
        }

        .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .score {
            font-size: 18px;
            margin-right: 20px;
        }

        .center-section {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 140px);
            /* Adjust based on header/footer height */
        }

        .annotation-app {
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 800px;
        }

        .annotation-app img {
            max-width: 100%;
            border: 1px solid #ccc;
        }

        .annotation-controls {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .annotation-controls select,
        .annotation-controls button {
            padding: 10px;
            font-size: 16px;
        }

        footer {
            background-color: #1976D2;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body>
    <header>
        <div class="profile">
            <img src="https://png.pngtree.com/png-clipart/20231019/original/pngtree-user-profile-avatar-png-image_13369988.png" alt="Profile Picture">
            <div class="username"><?php echo $first_name . " " . $last_name; ?></div>
        </div>
        <div class="score">Score: <span id="score">0</span></div>
    </header>

    <main>
        <section class="center-section">
            <div class="annotation-app">
                <img src="dataset/raw/sample.jpg" alt="Image to annotate" id="annotation-image">
                <div class="annotation-controls">
                    <select id="annotation-label">
                        <option value="0">Label 0</option>
                        <option value="1">Label 1</option>
                    </select>
                    <button id="next-button">Next</button>
                </div>
            </div>
        </section>
    </main>

    <footer>
        © 2024 Your Company
    </footer>
</body>

<script>
    // Placeholder JavaScript for button functionality
    document.getElementById('next-button').addEventListener('click', function() {
        // Logic to handle next image and annotation
        alert('Next image!');
    });
</script>

</html>