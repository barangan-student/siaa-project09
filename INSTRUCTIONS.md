# How to run the project

This project is set up to run in GitHub Codespaces.

1.  **Start the PHP server:**
    Open a terminal in your Codespace and run the following command from the root of the project:
    ```bash
    php -S 0.0.0.0:8000 -t public
    ```
    This will start the PHP built-in web server. GitHub Codespaces will automatically detect that you've started a server and will forward the port for you. You should see a notification in the bottom right corner of VS Code with a button to open the URL in your browser.

2.  **Access the application:**
    If you don't see the notification, you can manually open the URL. The URL will be in the format `https://<your-codespace-name>-8000.app.github.dev/login.php`.
    You can also go to the "Ports" tab in VS Code to find the forwarded URL.

## Default Credentials

-   **Admin:**
    -   Username: `admin`
    -   Password: `password`
-   **Employee:**
    -   Username: `employee`
    -   Password: `password`

## How to Test

1.  **Login as Admin:**
    -   Use the admin credentials to log in.
    -   You should be redirected to the `admin_dashboard.php`.

2.  **Login as Employee:**
    -   Use the employee credentials to log in.
    -   You should be redirected to the `employee_dashboard.php`.

3.  **Access Control:**
    -   While logged in as an employee, try to access the admin dashboard URL. You should be redirected to the `access_denied.php` page.
    -   While not logged in, try to access either `admin_dashboard.php` or `employee_dashboard.php`. You should be redirected to the `login.php` page.
