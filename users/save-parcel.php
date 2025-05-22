<?php
include 'db_connect.php'; // Include database connection first

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['login_id'])) {
    // Redirect to the correct login file location
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['login_id']; // Use the logged-in user's ID

// Fetch user information from the `solomon` database
$user_conn = new mysqli('localhost', 'root', '', 'solomon');
if ($user_conn->connect_error) {
    die("Connection to user database failed: " . $user_conn->connect_error);
}

$user_check = $user_conn->prepare("SELECT id, first_name, last_name, email FROM users WHERE id = ?");
$user_check->bind_param("i", $user_id);
$user_check->execute();
$user_check_result = $user_check->get_result();

if ($user_check_result->num_rows === 0) {
    echo '<div class="alert alert-danger" role="alert">Error: The logged-in user does not exist in the user database.</div>';
    exit();
}

$user_info = $user_check_result->fetch_assoc();
$user_check->close();
$user_conn->close();

// Switch to `cms_db` database
$conn->select_db('cms_db');

// Validate that the user_id exists in the `cms_db.users` table
$user_check_cms = $conn->prepare("SELECT id FROM users WHERE id = ?");
$user_check_cms->bind_param("i", $user_id);
$user_check_cms->execute();
$user_check_cms_result = $user_check_cms->get_result();

if ($user_check_cms_result->num_rows === 0) {
    // Insert the user into the `cms_db.users` table if they do not exist
    $insert_user_cms = $conn->prepare("INSERT INTO users (id, firstname, lastname, email, password, type, branch_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $default_password = $user_info['password'] ?? ''; // Use the password from the `solomon` database
    $default_type = 2; // Default to staff type
    $default_branch_id = 0; // Default branch ID
    $insert_user_cms->bind_param("issssii", $user_id, $user_info['first_name'], $user_info['last_name'], $user_info['email'], $default_password, $default_type, $default_branch_id);

    if (!$insert_user_cms->execute()) {
        echo '<div class="alert alert-danger" role="alert">Error: Failed to add the user to the CMS database.</div>';
        exit();
    }
    $insert_user_cms->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $sender_name = $_POST['sender_name'] ?? $user_info['first_name'] . ' ' . $user_info['last_name'];
    $sender_contact = $_POST['sender_contact'] ?? '';
    $sender_address = $_POST['sender_address'] ?? '';
    $recipient_name = $_POST['recipient_name'] ?? '';
    $recipient_contact = $_POST['recipient_contact'] ?? '';
    $recipient_address = $_POST['recipient_address'] ?? ''; // Ensure this is handled as a string
    $type = $_POST['type'] ?? '';
    $from_branch_id = isset($_POST['from_branch_id']) ? intval($_POST['from_branch_id']) : 0;
    $to_branch_id = isset($_POST['to_branch_id']) ? intval($_POST['to_branch_id']) : 0;
    $weight = isset($_POST['weight']) ? floatval($_POST['weight']) : 0.0;
    $height = isset($_POST['height']) ? floatval($_POST['height']) : 0.0;
    $width = isset($_POST['width']) ? floatval($_POST['width']) : 0.0;
    $length = isset($_POST['length']) ? floatval($_POST['length']) : 0.0;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;
    $payment_type_id = isset($_POST['payment_method']) ? intval($_POST['payment_method']) : null;

    if (!$payment_type_id) {
        die("Error: Payment method is required.");
    }

    // Debugging: Log the raw payment method value from POST
    error_log("Raw Payment Method from POST: " . $payment_type_id);

    // Validate the payment type ID using $logistics_db
    $payment_type_check = $logistics_db->prepare("SELECT id FROM payment_types WHERE id = ?");
    $payment_type_check->bind_param("i", $payment_type_id);
    $payment_type_check->execute();
    $payment_type_result = $payment_type_check->get_result();

    if ($payment_type_result->num_rows === 0) {
        die("Error: Invalid payment method selected.");
    }

    $payment_type_check->close();

    // Debugging: Log the mapped payment method value
    error_log("Mapped Payment Method: " . $payment_type_id);

    $delivery_branch_id = isset($_POST['delivery_branch_id']) ? intval($_POST['delivery_branch_id']) : null;
    $pickup_branch_id = isset($_POST['pickup_branch_id']) ? intval($_POST['pickup_branch_id']) : null;

    // Validate branch IDs based on type
    if ($type == 1 && !$delivery_branch_id) {
        die("Error: Delivery branch is required for Deliver type.");
    }
    if ($type == 2 && !$pickup_branch_id) {
        die("Error: Pickup branch is required for Pickup type.");
    }

    // Generate a unique reference number
    $reference_number = uniqid();

    // Set default status
    $status = 0;

    // Check if the parcels table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'parcels'");
    if ($table_check->num_rows === 0) {
        die("Error: The 'parcels' table does not exist in cms_db. Please check your database schema.");
    }

    // Determine the from and to branch IDs based on type
    $from_branch_id = $type == 1 ? $delivery_branch_id : $pickup_branch_id;
    $to_branch_id = $type == 2 ? $pickup_branch_id : $delivery_branch_id;

    // Insert parcel details into the logistics database
    $stmt = $conn->prepare("INSERT INTO parcels (user_id, reference_number, sender_name, sender_contact, sender_address, recipient_name, recipient_address, recipient_contact, type, from_branch_id, to_branch_id, weight, height, width, length, price, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssiiiidddsiis", $user_id, $reference_number, $sender_name, $sender_contact, $sender_address, $recipient_name, $recipient_address, $recipient_contact, $type, $from_branch_id, $to_branch_id, $weight, $height, $width, $length, $price, $payment_type_id, $status);

    // Debugging: Log the prepared statement error (if any)
    if (!$stmt) {
        error_log("Prepared Statement Error: " . $conn->error);
    }

    if ($stmt->execute()) {
        echo '<div class="alert alert-success" role="alert">Parcel saved successfully! Redirecting to your orders...</div>';
        header("refresh:3;url=view-orders.php"); // Redirect after 3 seconds
        exit();
    } else {
        echo '<div class="alert alert-danger" role="alert">An error occurred while saving the parcel. Please try again later.</div>';
        error_log("Database Error: " . $stmt->error); // Log error for debugging
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: create-parcel.php");
    exit();
}
?>