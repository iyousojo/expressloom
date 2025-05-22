<?php
ob_start();
date_default_timezone_set("Asia/Manila");

include 'admin_class.php';
include 'db_connect.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;
$crud = new Action();

if ($action == 'login') {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Hash the provided password using MD5

    // Debugging: Check if email and password are received
    error_log("Login attempt with email: $email");

    // Check if the email exists
    $email_query = $logistics_db->prepare("SELECT * FROM users WHERE email = ?");
    $email_query->bind_param("s", $email);
    $email_query->execute();
    $email_result = $email_query->get_result();

    if ($email_result->num_rows > 0) {
        // Email exists, now retrieve the password
        $user = $email_result->fetch_assoc();
        $db_password = $user['password']; // Get the hashed password from the database

        // Compare the provided hashed password with the stored hashed password
        if ($password === $db_password) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start(); // Start session only if not already active
            }
            $_SESSION['login_id'] = $user['id'];
            $_SESSION['login_name'] = $user['firstname'] . ' ' . $user['lastname'];
            $_SESSION['login_firstname'] = $user['firstname'];
            $_SESSION['login_type'] = $user['type'];
            $_SESSION['login_email'] = $user['email'];
            $_SESSION['login_branch_id'] = $user['branch_id'];
            echo 1; // Login successful
        } else {
            error_log("Login failed: Incorrect password for email: $email");
            echo "Incorrect password"; // Password is incorrect
        }
    } else {
        error_log("Login failed: Email not found - $email");
        echo "Email not found"; // Email does not exist
    }
    $email_query->close();
}

if ($action == 'logout') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_destroy();
    echo 1; // Logout successful
}

if ($action == 'save_user') {
    $save = $crud->save_user();
    echo $save;
}

if ($action == 'update_user') {
    $save = $crud->update_user();
    echo $save;
}

if ($action == 'delete_user') {
    $delete = $crud->delete_user();
    echo $delete;
}

if ($action == 'save_branch') {
    $save = $crud->save_branch();
    echo $save;
}

if ($action == 'delete_branch') {
    $delete = $crud->delete_branch();
    echo $delete;
}

if ($action == 'save_parcel') {
    $save = $crud->save_parcel();
    echo $save;
}

if ($action == 'delete_parcel') {
    $delete = $crud->delete_parcel();
    echo $delete;
}

if ($action == 'update_parcel') {
    $update = $crud->update_parcel();
    echo $update;
}

if ($action == 'get_parcel_heistory') {
    $history = $crud->get_parcel_heistory();
    echo $history;
}

if ($action == 'get_report') {
    $report = $crud->get_report();
    echo $report;
}

if ($action == 'save_system_settings') {
    $save = $crud->save_system_settings();
    echo $save;
}

ob_end_flush();