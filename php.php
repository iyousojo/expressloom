<?php
include_once "link.php";

$conn = get_database_connection();
if ($conn->connect_error) {
    die("Could not connect to mysql" . mysqli_error($conn));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = $_POST['address'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $zip_code = $_POST['zip_code'];
    $account_type = $_POST['account_type'];

    // Server-side validation for email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }

    // Server-side validation for password
    elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $password)) {
        $error = "Password must be at least 8 characters long and include one uppercase letter, one lowercase letter, and one number.";
    }

    // Check if passwords match
    elseif ($password != $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, email, password, address, state, country, zip_code, account_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $first_name, $middle_name, $last_name, $email, $passwordHash, $address, $state, $country, $zip_code, $account_type);
        if ($stmt->execute()) {
            $success = "User registered successfully.";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>