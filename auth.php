<?php
/**
 * A self-contained PHP authentication library for user authentication and group-based authorization.
 *
 * This library is designed to be easily integrated into any PHP project to provide a robust and secure
 * authentication system. It uses a SQLite database to store user data and manages sessions for
 * logged-in users.
 *
 * USAGE:
 * 1. Place this file in your project (e.g., in a 'src' or 'includes' directory).
 * 2. In your PHP files that need authentication, include this file using `require_once`.
 * 3. Call the `init()` function to set the path to your database file.
 *    Make sure to start the session at the top of your files with `session_start()`.
 * 4. Use the provided functions (loginUser, logoutUser, isLoggedIn, getCurrentUserId, isUserInGroup) 
 *    to manage authentication and authorization in your application.
 *
 * CONFIGURATION:
 * - The database file path is configured using the `init()` function. By default, it points to
 *   `database/database.db`. You can change this path to suit your project structure.
 */

// --- INTERNAL DATABASE FUNCTIONS ---

/**
 * Establishes a connection to the SQLite database.
 * @internal
 * @return PDO The PDO database connection object.
 */
$dbPath = 'database/database.db';

function getDBConnection() {
    global $dbPath;
    try {
        // The path to the database file. You can change this to a different location.
        $db = new PDO('sqlite:' . $dbPath);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'could not find driver') !== false) {
            die("Error: The PDO SQLite driver is not installed. Please enable the pdo_sqlite extension in your php.ini file.");
        } else {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}

/**
 * Initializes the database with the required tables and default data.
 * @internal
 */
function initializeDatabase() {
    $db = getDBConnection();
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS groups (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL UNIQUE
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS user_groups (
        user_id INTEGER,
        group_id INTEGER,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (group_id) REFERENCES groups(id),
        PRIMARY KEY (user_id, group_id)
    )");

    // Add default admin and employee groups if they don't exist
    $stmt = $db->prepare("INSERT OR IGNORE INTO groups (name) VALUES (?)");
    $stmt->execute(['Admin']);
    $stmt->execute(['Employee']);

    // Add a default admin user if it doesn't exist
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    if (!$stmt->fetchColumn()) {
        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', password_hash('password', PASSWORD_DEFAULT)]);
        $user_id = $db->lastInsertId();

        $stmt = $db->prepare("SELECT id FROM groups WHERE name = ?");
        $stmt->execute(['Admin']);
        $group_id = $stmt->fetchColumn();

        $stmt = $db->prepare("INSERT INTO user_groups (user_id, group_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $group_id]);
    }
    
    // Add a default employee user if it doesn't exist
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute(['employee']);
    if (!$stmt->fetchColumn()) {
        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute(['employee', password_hash('password', PASSWORD_DEFAULT)]);
        $user_id = $db->lastInsertId();

        $stmt = $db->prepare("SELECT id FROM groups WHERE name = ?");
        $stmt->execute(['Employee']);
        $group_id = $stmt->fetchColumn();

        $stmt = $db->prepare("INSERT INTO user_groups (user_id, group_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $group_id]);
    }
}

// --- PUBLIC AUTHENTICATION FUNCTIONS ---

/**
 * Initializes the authentication library and sets the database path.
 *
 * @param string $path The path to the SQLite database file.
 */
function init($path) {
    global $dbPath;
    $dbPath = $path;
    initializeDatabase();
}


/**
 * Logs in a user by verifying their credentials and setting session variables.
 *
 * @param string $username The username to log in.
 * @param string $password The password to verify.
 * @return bool True if the login is successful, false otherwise.
 */
function loginUser($username, $password) {
    $username = filter_var($username, FILTER_SANITIZE_STRING);
    if (!is_string($password)) {
        return false;
    }

    $db = getDBConnection();
    $stmt = $db->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        return true;
    }

    return false;
}

/**
 * Logs out the current user by destroying the session.
 */
function logoutUser() {
    session_unset();
    session_destroy();
}

/**
 * Gets the primary group of a user.
 *
 * @param int $userId The ID of the user.
 * @return string|null The name of the user's primary group, or null if not found.
 */
function getUserPrimaryGroup($userId) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT g.name FROM groups g JOIN user_groups ug ON g.id = ug.group_id WHERE ug.user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}

/**
 * Gets the ID of the currently logged-in user.
 *
 * @return int|null The user ID, or null if not logged in.
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Checks if a user is currently logged in.
 *
 * @return bool True if the user is logged in, false otherwise.
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Checks if a user belongs to a specific group.
 *
 * @param int $userId The ID of the user.
 * @param string $group The name of the group to check.
 * @return bool True if the user is in the group, false otherwise.
 */
function isUserInGroup($userId, $group) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT COUNT(*) FROM user_groups ug JOIN groups g ON ug.group_id = g.id WHERE ug.user_id = ? AND g.name = ?");
    $stmt->execute([$userId, $group]);
    return $stmt->fetchColumn() > 0;
}
?>