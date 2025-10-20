### **Finalized Product Requirements Document (PRD)**

**Version:** 1.1  
**Date:** October 20, 2025  
**Author:** Gemini Assistant

#### **1. Overview**

This document outlines the requirements for a self-contained, pluggable PHP authentication library. The primary goal is to create a single PHP file (`auth.php`) that can be easily integrated into any new or existing PHP project to provide robust, secure, and group-based user authentication and authorization. The library will be supported by simple frontend pages to demonstrate its functionality using **Admin** and **Employee** user roles.

#### **2. Project Goals & Objectives**

*   **Reusability:** To create a single, self-contained library file that is easy to drop into any project.
*   **Security:** To ensure industry-standard security practices are followed, specifically for password storage and session management.
*   **Simplicity:** To maintain a minimal and simple technology stack, avoiding unnecessary frameworks or dependencies.
*   **Flexibility:** To implement a role-based access control (RBAC) system using user groups, allowing for granular control over application access.
*   **Demonstrability:** To build a functional prototype that clearly showcases all core features for evaluation.

#### **3. User Personas & Roles**

This project has two primary user types:

1.  **The Developer:** The individual who will integrate the `auth.php` library into their application. They need clear functions and easy configuration.
2.  **The End-User:** The individual who interacts with the application. Their experience is defined by their assigned role.
    *   **Admin Andy (Admin):** Has full access to all applications, including user management and system settings.
    *   **Employee Eric (Employee):** Has access only to general company applications (e.g., viewing announcements, internal documents) and cannot access administrative functions.

#### **4. Core Features & Requirements**

*(Sections 4.1, 4.2, 4.3, and 4.4 remain the same as the previous PRD, covering the Library Core, Security, Database, and API functions.)*

**4.5. Frontend & User Interface**
*   **Login Page:** A simple HTML form for users to log in.
*   **Admin Dashboard (`admin_dashboard.php`):** A distinct frontend for users in the "Admin" group. It shall display links to all management applications.
*   **Employee Dashboard (`employee_dashboard.php`):** A distinct frontend for users in the "Employee" group. It shall only display links to general applications they are permitted to access (e.g., "Company Announcements").
*   **Access Denied Page (`access_denied.php`):** A generic, user-friendly page that is displayed when a user attempts to access a URL they do not have permission for. It **must** return a `403 Forbidden` HTTP status code.
*   **Dynamic Navigation:** Menus and navigation links **should** be rendered conditionally based on the logged-in user's permissions. Links to inaccessible apps should not be displayed.

#### **5. Technical Stack**

*(This section remains the same: PHP, SQLite, HTML/CSS, optional vanilla JS.)*

#### **6. Out of Scope**

*(This section remains the same.)*

#### **7. Success Metrics & Demonstration**

The project will be considered successful when the following can be demonstrated:
1.  A new user can be created and assigned to the "Employee" group.
2.  An admin can log in and be redirected to the `admin_dashboard.php`.
3.  An employee can log in and be redirected to the `employee_dashboard.php`.
4.  The employee **cannot** access the admin dashboard by typing the URL directly and is instead shown the "Access Denied" page.
5.  The admin **can** access both the admin and employee dashboards.
6.  A non-logged-in user is redirected to the login page when attempting to access any dashboard.
7.  A code review of `auth.php` confirms it is self-contained and all password logic is secure.

---

### **Updated Code for Frontends**

Here is how you would implement the simple, distinct frontends for the **Admin** and **Employee** roles.

#### **1. Updated Login Redirection Logic**

In your file that processes the login, update the `switch` statement:

```php
// In your login processing file...

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
            header('Location: generic_dashboard.php');
            break;
    }
    exit();
} // ...
```

#### **2. Admin Dashboard (No Change)**

The `admin_dashboard.php` file remains largely the same, as its purpose is still administrative. Just ensure the security check is looking for the "Admin" group.

```php
// At the top of admin_dashboard.php
if (!isLoggedIn() || !isUserInGroup($_SESSION['user_id'], 'Admin')) {
    header('Location: access_denied.php');
    exit();
}
```

#### **3. New Employee Dashboard**

Create this new file, `employee_dashboard.php`. It has a different style and provides links relevant to a general employee.

**`employee_dashboard.php`**
```php
<?php
session_start();
require_once 'auth.php';

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
        <nav>
            <ul>
                <li><a href="/announcements.php">Company Announcements</a></li>
                <li><a href="/documents.php">Internal Documents</a></li>
                <li><a href="/timesheet.php">Submit Timesheet</a></li>
            </ul>
        </nav>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
```