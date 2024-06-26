<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        die("User not logged in.");
    }

    // Get user ID from session
    $user_id = $_SESSION['user_id'];

    $imageName = $_POST['image'];
    $annotationData = $_POST['data'];
    $rawDir = 'dataset/raw';
    $trainDir = 'dataset/annotated/train';
    $annotatedDir = "$trainDir/labels";
    $annotatedImagesDir = "$trainDir/images";

    // Ensure the directories exist
    if (!is_dir($annotatedDir)) {
        if (!mkdir($annotatedDir, 0777, true)) {
            die("Failed to create directory: $annotatedDir");
        }
    }

    if (!is_dir($annotatedImagesDir)) {
        if (!mkdir($annotatedImagesDir, 0777, true)) {
            die("Failed to create directory: $annotatedImagesDir");
        }
    }

    // Write annotation data to file
    $baseName = pathinfo($imageName, PATHINFO_FILENAME);
    $annotationFile = "$annotatedDir/$baseName.txt";
    if (file_put_contents($annotationFile, $annotationData, FILE_APPEND) === false) {
        die("Failed to write to file: $annotationFile");
    }

    // Move the image to the annotated images directory if not already moved
    $newImagePath = "$annotatedImagesDir/" . basename($imageName);
    if (!file_exists($newImagePath)) {
        if (!rename($imageName, $newImagePath)) {
            die("Failed to move image to annotated directory: $newImagePath");
        }
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

    // Use a transaction to ensure credit is only incremented once
    $conn->begin_transaction();

    try {
        // Record the annotation in the database
        $insert_annotation_query = "INSERT INTO annotations (user_id, image_path) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_annotation_query);
        $stmt->bind_param("is", $user_id, $newImagePath);
        $stmt->execute();
        $stmt->close();

        // Increment user's credit by 1
        $update_credit_query = "UPDATE users SET credit = credit + 1 WHERE id=?";
        $stmt = $conn->prepare($update_credit_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Get updated credit
        $get_credit_query = "SELECT credit FROM users WHERE id=?";
        $stmt = $conn->prepare($get_credit_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($new_credit);
        $stmt->fetch();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        echo "$new_credit";
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        die("Failed to update credit: " . $conn->error);
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
