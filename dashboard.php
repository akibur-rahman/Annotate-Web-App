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
    $credit = $user['credit'];
} else {
    // Redirect to login page if user not found
    header("Location: login.php");
    exit();
}

// Retrieve annotated images for the user
$get_annotations_query = "SELECT image_path FROM annotations WHERE user_id='$user_id'";
$annotations_result = $conn->query($get_annotations_query);

$annotated_images = [];
if ($annotations_result->num_rows > 0) {
    while ($row = $annotations_result->fetch_assoc()) {
        $image_path = $row['image_path'];
        $annotation_path = str_replace('/images/', '/labels/', $image_path);
        $annotation_path = str_replace('.jpg', '.txt', $annotation_path);

        // Check if annotation file exists
        if (file_exists($annotation_path)) {
            $annotated_images[] = $image_path;
        }
    }
} else {
    $annotated_images = [];
}

$conn->close();

// Define colors (at least 80 unique colors)
$colors = [
    '#FF5733', '#33FF57', // Red and Green
    '#3357FF', '#FF33A1', // Blue and Pink
    '#A133FF', '#33FFF5', // Purple and Cyan
    '#FF5733', '#F5FF33', // Red and Yellow
    '#5733FF', '#33FFA1', // Indigo and Mint
    '#FFA133', '#33FF33', // Orange and Lime
    '#FF3333', '#33FFFF', // Bright Red and Aqua
    '#FFFF33', '#3333FF', // Yellow and Blue
];

// Load the labels from data.yaml
require 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$rawDir = 'dataset/raw';
$dataYamlPath = "$rawDir/data.yaml";
$dataYaml = Yaml::parseFile($dataYamlPath);
$labelNames = $dataYaml['names'];

// Map labels to colors
$labelColors = [];
foreach ($labelNames as $index => $labelName) {
    $labelColors[$labelName] = $colors[$index % count($colors)];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
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
            background: linear-gradient(45deg, #1976D2, #64B5F6);
            color: white;
            padding: 16px 24px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile {
            display: flex;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 8px 16px;
        }

        .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .annotation-grid {
            padding: 24px;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: center;
        }

        .annotation-card {
            position: relative;
            width: 100%;
            max-width: 300px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .annotation-card img {
            width: 100%;
            display: block;
        }

        .annotation-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .button {
            background-color: #1976D2;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #1565C0;
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
    </style>
</head>

<body>
    <header>
        <div class="profile">
            <img src="https://png.pngtree.com/png-clipart/20231019/original/pngtree-user-profile-avatar-png-image_13369988.png" alt="Profile Picture">
            <div class="username"><?php echo htmlspecialchars($first_name . " " . $last_name); ?></div>
        </div>
        <div>
            <div class="score">Credit: <span id="credit"><?php echo htmlspecialchars($credit); ?></span></div>
        </div>
        <button class="button" onclick="location.href='app.php'">Annotate Images</button>
        <button class="logout-button" onclick="location.href='logout.php'">Logout</button>
    </header>

    <main>
        <section class="annotation-grid">
            <?php foreach ($annotated_images as $image_path) : ?>
                <div class="annotation-card">
                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Annotated Image">
                    <canvas class="annotation-overlay" data-image="<?php echo htmlspecialchars($image_path); ?>"></canvas>
                </div>
            <?php endforeach; ?>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvasElements = document.querySelectorAll('.annotation-overlay');
            const labelColors = <?php echo json_encode($labelColors); ?>;
            const labelNames = <?php echo json_encode($labelNames); ?>;

            canvasElements.forEach(canvas => {
                const imagePath = canvas.getAttribute('data-image');
                const annotationPath = imagePath.replace('/images/', '/labels/').replace('.jpg', '.txt');

                const image = new Image();
                image.src = imagePath;

                image.onload = function() {
                    canvas.width = image.width;
                    canvas.height = image.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(image, 0, 0, image.width, image.height);

                    fetch(annotationPath)
                        .then(response => response.text())
                        .then(data => {
                            const annotations = data.trim().split('\n').map(line => line.split(' '));

                            annotations.forEach(annotation => {
                                const [labelIndex, normX, normY, normWidth, normHeight] = annotation.map(Number);
                                const x = (normX - normWidth / 2) * canvas.width;
                                const y = (normY - normHeight / 2) * canvas.height;
                                const width = normWidth * canvas.width;
                                const height = normHeight * canvas.height;

                                const labelName = labelNames[labelIndex];
                                ctx.strokeStyle = labelColors[labelName] || '#000000';
                                ctx.lineWidth = 2;
                                ctx.strokeRect(x, y, width, height);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching annotation data:', error);
                        });
                };
            });
        });
    </script>
</body>

</html>