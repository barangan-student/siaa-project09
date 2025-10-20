<?php
session_start();
require_once '../auth.php';

init('../database/database.db');

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $retype_password = $_POST['retype_password'];
    $group = $_POST['group'];

    if (empty($username) || empty($password) || empty($retype_password) || empty($group)) {
        $error_message = 'All fields are required.';
    } else if ($password !== $retype_password) {
        $error_message = 'Passwords do not match.';
    } else {
        if (createUser($username, $password, $group)) {
            $success_message = 'Registration successful. You can now <a href="login.php">login</a>.';
        } else {
            $error_message = 'Username already exists.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .register-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        .register-form h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 0.9em;
        }

        .input-group input[type="text"],
        .input-group input[type="password"],
        .input-group select {
            width: calc(100% - 20px); /* Account for padding */
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
            font-size: 1em;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            width: 100%;
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .links {
            margin-top: 20px;
            font-size: 0.9em;
        }

        .links a {
            color: #007bff;
            text-decoration: none;
            margin: 0 8px;
        }

        .links a:hover {
            text-decoration: underline;
        }
        
        .error {
            color: red;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <form class="register-form" action="register.php" method="post">
            <h2>Register</h2>
            <?php if ($error_message): ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <p class="success"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="input-group">
                <label for="retype_password">Retype Password</label>
                <input type="password" id="retype_password" name="retype_password" placeholder="Retype your password" required>
            </div>
            <div class="input-group">
                <input type="checkbox" onclick="togglePasswordVisibility()"> Show Password
            </div>
            <div class="input-group">
                <label for="group">Group</label>
                <select id="group" name="group" required>
                    <option value="Employee">Employee</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <button type="submit">Register</button>
            <div class="links">
                <a href="login.php">Already have an account? Login</a>
            </div>
        </form>
    </div>

    <script>
        function togglePasswordVisibility() {
            var password = document.getElementById("password");
            var retype_password = document.getElementById("retype_password");
            if (password.type === "password") {
                password.type = "text";
                retype_password.type = "text";
            } else {
                password.type = "password";
                retype_password.type = "password";
            }
        }
    </script>
</body>
</html>
