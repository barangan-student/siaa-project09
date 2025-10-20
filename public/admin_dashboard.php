<?php
session_start();
require_once '../auth.php';

init('../database/database.db');

if (!isLoggedIn() || !isUserInGroup($_SESSION['user_id'], 'Admin')) {
    header('Location: access_denied.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <style> 
        body { font-family: Arial, Helvetica, sans-serif; background-color: #f8f9fa; }
        .container { border: 2px solid #dc3545; padding: 20px; margin: 20px; }
        h1 { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['username']; ?>!</p>
        <p>This is the admin dashboard. You have special powers!</p>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>