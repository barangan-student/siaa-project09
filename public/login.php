<?php
session_start();
require_once '../auth.php';

init('../database/database.db');

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (loginUser($username, $password)) {
        $userId = $_SESSION['user_id'];
        $group = getUserPrimaryGroup($userId);

        switch ($group) {
            case 'Admin':
                header('Location: admin_dashboard.php');
                break;
            case 'Employee':
                header('Location: employee_dashboard.php');
                break;
            default:
                header('Location: access_denied.php');
                break;
        }
        exit();
    } else {
        $error_message = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
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

        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        .login-form h2 {
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
        .input-group input[type="password"] {
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
    </style>
</head>
<body>
    <div class="login-container">
        <form class="login-form" action="login.php" method="post">
            <h2>Login</h2>
            <?php if ($error_message): ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit">Login</button>
            <div class="links">
                <a href="#">Forgot Password?</a>
                <a href="#">Sign Up</a>
            </div>
        </form>
    </div>
</body>
</html>