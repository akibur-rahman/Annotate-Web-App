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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@material/button@14.0.0/dist/mdc.button.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@material/layout-grid@14.0.0/dist/mdc.layout-grid.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
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
        }

        .annotation-grid .mdc-layout-grid__inner {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }

        .annotation-grid .mdc-layout-grid__cell {
            flex: 1 1 calc(33.333% - 16px);
            display: flex;
            justify-content: center;
            align-items: center;
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
        <div class="profile">
            <img src="https://png.pngtree.com/png-clipart/20231019/original/pngtree-user-profile-avatar-png-image_13369988.png" alt="Profile Picture">
            <div class="username"><?php echo htmlspecialchars($first_name . " " . $last_name); ?></div>
        </div>
        <div>
            <div class="score">Credit: <span id="credit"><?php echo htmlspecialchars($credit); ?></span></div>
        </div>
        <button class="mdc-button mdc-button--gradient" onclick="location.href='app.php'">Annotate Images</button>
    </header>

    <main>
        <section class="annotation-grid">
            <div class="mdc-layout-grid">
                <div class="mdc-layout-grid__inner">
                    <?php foreach ($annotated_images as $image_path) : ?>
                        <div class="mdc-layout-grid__cell">
                            <div class="annotation-card">
                                <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Annotated Image">
                                <canvas class="annotation-overlay" data-image="<?php echo htmlspecialchars($image_path); ?>"></canvas>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>
        © 2024 Your Company
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvasElements = document.querySelectorAll('.annotation-overlay');
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
                                const [label, normX, normY, normWidth, normHeight] = annotation.map(Number);
                                const x = (normX - normWidth / 2) * canvas.width;
                                const y = (normY - normHeight / 2) * canvas.height;
                                const width = normWidth * canvas.width;
                                const height = normHeight * canvas.height;

                                ctx.strokeStyle = 'red';
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