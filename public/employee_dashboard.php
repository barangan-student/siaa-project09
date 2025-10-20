<?php
session_start();
require_once '../auth.php';

init('../database/database.db');

// SECURITY: Protect this page for Employees
if (!isLoggedIn() || !isUserInGroup($_SESSION['user_id'], 'Employee')) {
    header('Location: access_denied.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee Dashboard</title>
    <style> 
        body { font-family: Arial, Helvetica, sans-serif; background-color: #eef; } 
        .container { border: 2px solid #6a5acd; padding: 20px; margin: 20px; } 
        h1 { color: #483d8b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Employee Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['username']; ?>!</p>
        <p>This is the employee dashboard. You can see company stuff here.</p>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>