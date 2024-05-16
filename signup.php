<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        /* Resetting default margin and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            width: 350px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .input-field {
            margin-bottom: 20px;
        }

        .input-field label {
            display: block;
            margin-bottom: 5px;
        }

        .input-field input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn {
            background-color: #1976D2;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #1565C0;
        }

        .already-logged-in {
            text-align: center;
            margin-top: 10px;
        }

        .already-logged-in a {
            color: #1976D2;
            text-decoration: none;
        }

        .already-logged-in a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Sign Up</h2>
        <form action="signup_process.php" method="post">
            <div class="input-field">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="input-field">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div class="input-field">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-field">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-field">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="input-field">
                <button class="btn" type="submit">Sign Up</button>
            </div>
        </form>
        <div class="already-logged-in">
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>

</html>