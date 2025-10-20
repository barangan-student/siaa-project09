<?php
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Access Denied</title>
    <style> 
        body { font-family: Arial, Helvetica, sans-serif; background-color: #fff; color: #dc3545; text-align: center; padding-top: 50px;} 
        h1 { font-size: 48px; } 
    </style>
</head>
<body>
    <h1>403</h1>
    <h2>Access Denied</h2>
    <p>You do not have permission to access this page.</p>
    <a href="login.php">Back to Login</a>
</body>
</html>