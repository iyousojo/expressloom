<?php 
include('db_connect.php'); 

// Redirect to login.php if the user is not logged in
if (!isset($_SESSION['login_id'])) {
    header('Location: login.php'); // Updated to login.php
    exit();
}

// Save user preference for the home page
$_SESSION['preferred_page'] = 'home';

// Fetch user-specific content
$user_id = $_SESSION['login_id'];
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $user = []; // Default to an empty array if the query fails
}
?>
<div class="container">
    <h2>Welcome,
        <?php echo isset($user['first_name']) ? $user['first_name'] : 'Guest'; ?>
        <?php echo isset($user['last_name']) ? $user['last_name'] : ''; ?>!
    </h2>
</div>