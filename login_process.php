<?php
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

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Retrieve user from database
    $retrieve_user_query = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($retrieve_user_query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            echo "Login successful!";
            // Redirect to dashboard or home page
            // header("Location: dashboard.php");
            exit();
        } else {
            echo "Error: Incorrect password.";
        }
    } else {
        echo "Error: User not found.";
    }
}

$conn->close();
