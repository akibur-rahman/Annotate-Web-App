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

// Define image path
$rawDir = 'dataset/raw';
$images = glob("$rawDir/*.jpg");
if (count($images) > 0) {
    $imagePath = $images[0]; // Get the first image for annotation
} else {
    // If no images found, redirect to error page or display a message
    exit("No images found for annotation.");
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
        }

        .annotation-app {
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 10px;
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
            justify-content: space-between;
            align-items: center;
        }

        .annotation-controls .mdl-button {
            margin-right: 10px;
            margin-left: 10px;
        }

        #canvas {
            z-index: 2;
            border: 1px solid #ccc;
            max-width: 100%;
            max-height: 100%;
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
            <div class="username"><?php echo htmlspecialchars($first_name . " " . $last_name); ?></div>
        </div>
        <div class="score">Score: <span id="score">0</span></div>
    </header>

    <main>
        <section class="center-section">
            <div class="annotation-app">
                <canvas id="canvas"></canvas>
                <div class="annotation-controls">
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <select id="annotation-label" class="mdl-textfield__input">
                            <option value="0">Label 0</option>
                            <option value="1">Label 1</option>
                        </select>
                        <label class="mdl-textfield__label" for="annotation-label"></label>
                    </div>
                    <button id="done-button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">
                        Done
                    </button>
                    <button id="next-button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--primary">
                        Next
                    </button>
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
            const images = <?php echo json_encode($images); ?>; // Array of image paths
            let annotations = []; // Array to store annotations for the current image
            let isDrawing = false;
            let startX, startY, endX, endY;

            function loadImage(index) {
                const img = new Image();
                img.src = images[index];
                const canvas = document.getElementById('canvas');
                const ctx = canvas.getContext('2d');

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
                });
            }

            function saveAnnotation(x1, y1, x2, y2) {
                const label = document.getElementById('annotation-label').value;
                const canvas = document.getElementById('canvas');
                const width = canvas.width;
                const height = canvas.height;

                const normX = (x1 + x2) / 2 / width;
                const normY = (y1 + y2) / 2 / height;
                const normWidth = Math.abs(x2 - x1) / width;
                const normHeight = Math.abs(y2 - y1) / height;

                const annotationData = `${label} ${normX} ${normY} ${normWidth} ${normHeight}`;
                annotations.push(annotationData); // Store annotation in the array

                console.log(`Annotation saved: ${annotationData}`);
            }

            document.getElementById('done-button').addEventListener('click', () => {
                if (startX !== undefined && startY !== undefined && endX !== undefined && endY !== undefined) {
                    saveAnnotation(startX, startY, endX, endY);
                }
            });

            document.getElementById('next-button').addEventListener('click', function() {
                if (annotations.length > 0) {
                    const annotationString = annotations.join('\n');

                    // Send all accumulated annotations to the server
                    fetch('save_annotation.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `image=${encodeURIComponent(images[currentIndex])}&data=${encodeURIComponent(annotationString)}`
                        }).then(response => response.text())
                        .then(data => console.log(data))
                        .catch(error => console.error('Error:', error));

                    annotations = []; // Clear the annotations array for the next image
                }

                // Increment index to load the next image
                currentIndex = (currentIndex + 1) % images.length;
                loadImage(currentIndex); // Load the next image
            });

            loadImage(currentIndex); // Load the first image initially
        });
    </script>
</body>

</html>