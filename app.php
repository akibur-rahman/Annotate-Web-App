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

// Define image path
$rawDir = 'dataset/raw';
$images = glob("$rawDir/*.jpg");
if (count($images) > 0) {
    $imagePath = $images[0]; // Get the first image for annotation
} else {
    // If no images found, redirect to error page or display a message
    exit("No images found for annotation.");
}

// Parse data.yaml for label names
require 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$dataYamlPath = "$rawDir/data.yaml";
$dataYaml = Yaml::parseFile($dataYamlPath);
$labelNames = $dataYaml['names'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App</title>
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

        .center-section {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 200px);
            padding: 24px;
        }

        .annotation-app {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 24px;
            border-radius: 8px;
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .annotation-app img {
            display: none;
        }

        .annotation-controls {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
        }

        #canvas {
            z-index: 2;
            border: 1px solid #ccc;
            max-width: 100%;
            max-height: 100%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

        .button {
            background-color: #1976D2;
            color: white;
            border-radius: 4px;
            padding: 8px 16px;
            text-transform: uppercase;
            font-weight: bold;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .button:hover {
            background-color: #1565C0;
        }

        .button--gradient {
            background: linear-gradient(45deg, #1976D2, #64B5F6);
        }

        .button--gradient:hover {
            background: linear-gradient(45deg, #1565C0, #42A5F5);
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
        <button class="button button--gradient" onclick="location.href='dashboard.php'">Dashboard</button>
    </header>

    <main>
        <section class="center-section">
            <div class="annotation-app">
                <img src="" id="image-to-annotate" style="display: none;">
                <canvas id="canvas"></canvas>
                <div class="annotation-controls">
                    <select id="annotation-label">
                        <?php foreach ($labelNames as $index => $labelName) : ?>
                            <option value="<?php echo $index; ?>"><?php echo htmlspecialchars($labelName); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button id="done-button" class="button button--gradient">Done</button>
                    <button id="next-button" class="button button--gradient">Next</button>
                </div>
            </div>
        </section>
    </main>

    <footer>
        © 2024 Your Company
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            let currentIndex = 0; // Initialize index to track current image
            const images = <?php echo json_encode(array_values($images)); ?>; // Array of image paths

            function loadImage(index) {
                const img = new Image();
                img.src = images[index];
                const canvas = document.getElementById('canvas');
                const ctx = canvas.getContext('2d');
                let isDrawing = false;
                let annotations = [];
                let startX, startY, endX, endY;

                img.onload = function() {
                    canvas.width = img.width; // Set canvas width to match image width
                    canvas.height = img.height; // Set canvas height to match image height
                    ctx.drawImage(img, 0, 0, img.width, img.height); // Draw image on canvas with image dimensions
                };

                canvas.addEventListener('mousedown', (e) => {
                    const rect = canvas.getBoundingClientRect();
                    startX = e.clientX - rect.left;
                    startY = e.clientY - rect.top;
                    isDrawing = true;
                });

                canvas.addEventListener('mousemove', (e) => {
                    if (isDrawing) {
                        const rect = canvas.getBoundingClientRect();
                        endX = e.clientX - rect.left;
                        endY = e.clientY - rect.top;
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.drawImage(img, 0, 0, img.width, img.height); // Redraw the image to clear any previous annotations
                        ctx.strokeRect(startX, startY, endX - startX, endY - startY);
                    }
                });

                canvas.addEventListener('mouseup', () => {
                    isDrawing = false;
                    const selectedLabelIndex = document.getElementById('annotation-label').selectedIndex;
                    const selectedLabelValue = document.getElementById('annotation-label').options[selectedLabelIndex].value;
                    annotations.push({
                        startX,
                        startY,
                        endX,
                        endY,
                        label: selectedLabelValue // Save the selected label with annotation
                    });
                });

                function saveAnnotations() {
                    const width = canvas.width;
                    const height = canvas.height;

                    let annotationData = '';

                    annotations.forEach(annotation => {
                        const {
                            startX,
                            startY,
                            endX,
                            endY,
                            label
                        } = annotation;
                        const normX = (startX + endX) / 2 / width;
                        const normY = (startY + endY) / 2 / height;
                        const normWidth = Math.abs(endX - startX) / width;
                        const normHeight = Math.abs(endY - startY) / height;
                        annotationData += `${label} ${normX} ${normY} ${normWidth} ${normHeight}\n`;
                    });

                    // Send the annotation data to the server to save it
                    fetch('save_annotation.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `image=${encodeURIComponent(images[index])}&data=${encodeURIComponent(annotationData)}`
                        })
                        .then(response => response.text())
                        .then(newCredit => {
                            document.getElementById('credit').textContent = newCredit;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }

                // Add event listener to the "Next" button
                const nextButton = document.getElementById('next-button');
                nextButton.onclick = () => {
                    saveAnnotations();
                    currentIndex = (currentIndex + 1) % images.length;
                    annotations = [];
                    loadImage(currentIndex); // Load the next image
                };

                // Add event listener to the "Done" button
                const doneButton = document.getElementById('done-button');
                doneButton.onclick = () => {
                    saveAnnotations();
                    annotations = []; // Clear annotations after saving
                };
            }

            loadImage(currentIndex); // Load the first image initially
        });
    </script>


</body>

</html>