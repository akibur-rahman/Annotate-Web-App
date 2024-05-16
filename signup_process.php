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
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Error: Passwords do not match.";
        exit();
    }

    // Check if email is already registered
    $check_email_query = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($check_email_query);
    if ($result->num_rows > 0) {
        echo "Error: Email is already registered.";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $insert_user_query = "INSERT INTO users (first_name, last_name, email, password) VALUES ('$first_name', '$last_name', '$email', '$hashed_password')";
    if ($conn->query($insert_user_query) === TRUE) {
        header("Location: login.php");
    } else {
        echo "Error: " . $insert_user_query . "<br>" . $conn->error;
    }
}

$conn->close();
