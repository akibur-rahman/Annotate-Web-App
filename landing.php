<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Dataset Annotation App</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
        }

        header {
            background-color: #1976D2;
            /* Material Design Blue */
            color: white;
            padding: 10px;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-logo img {
            width: 40px;
            height: auto;
        }

        .navbar-buttons button {
            background-color: transparent;
            border: none;
            color: white;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
        }

        .navbar-buttons button:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 100px);
        }

        .center-section {
            text-align: center;
        }

        .center-section img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }

        .center-section h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .center-section p {
            font-size: 16px;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="navbar-logo">
                <h3 style="font-family:'Times New Roman', Times, serif">Annotate</h3>
            </div>
            <div class="navbar-buttons">
                <a href="login.php"><button id="loginBtn">Login</button></a>
                <a href="signup.php"><button id="signupBtn">Signup</button></a>
            </div>
        </nav>
    </header>

    <main>
        <section class="center-section">
            <img src="https://assets.markup.io/app/uploads/2023/04/mcmarvin.gif" alt="Image">
            <h1>Welcome to our Image Dataset Annotation App</h1>
            <p>Start annotating your images to build powerful datasets for your projects.</p>
        </section>
    </main>
</body>

</html>