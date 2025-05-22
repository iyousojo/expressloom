<?php
include 'db_connect.php';
include_once 'sidebar.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['login_id'];

// Correct the SQL query to use the `payment_method` table and column
$parcels_query = $conn->prepare("SELECT p.reference_number, p.sender_name, p.recipient_name, p.type, p.weight, p.price, pm.payment_method AS payment_method, b_from.city AS from_branch, b_to.city AS to_branch, p.date_created 
    FROM cms_db.parcels p
    LEFT JOIN cms_db.branches b_from ON p.from_branch_id = b_from.id
    LEFT JOIN cms_db.branches b_to ON p.to_branch_id = b_to.id
    LEFT JOIN cms_db.payment_method pm ON p.payment_method_id = pm.id
    WHERE p.user_id = ?
    ORDER BY p.date_created DESC");
if (!$parcels_query) {
    die("Error preparing parcels query: " . $conn->error);
}
$parcels_query->bind_param("i", $user_id);
$parcels_query->execute();
$parcels_result = $parcels_query->get_result();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>View Orders | ExpressLoom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f6f9;
        display: flex;
    }

    .container {
        margin: 100px auto;
        padding: 30px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 1200px;
        width: 100%;
        overflow: auto;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #007bff;
        font-size: 28px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    table th,
    table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    table th {
        background-color: #007bff;
        color: white;
    }

    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    table tr:hover {
        background-color: #f1f1f1;
    }

    .alert {
        padding: 15px;
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
        text-align: center;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2><i class="fas fa-box"></i> Your Orders</h2>
        <?php if ($parcels_result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Reference Number</th>
                    <th>Sender</th>
                    <th>Recipient</th>
                    <th>Type</th>
                    <th>Weight</th>
                    <th>Price</th>
                    <th>Payment Method</th>
                    <th>From Branch</th>
                    <th>To Branch</th>
                    <th>Date Created</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($parcel = $parcels_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($parcel['reference_number'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($parcel['sender_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($parcel['recipient_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo $parcel['type'] == 1 ? 'Deliver' : ($parcel['type'] == 2 ? 'Pickup' : 'Unknown'); ?>
                    </td>
                    <td><?php echo htmlspecialchars($parcel['weight'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars(number_format($parcel['price'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($parcel['payment_method'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($parcel['from_branch'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($parcel['to_branch'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($parcel['date_created'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="alert">
            <i class="fas fa-info-circle"></i> You have no orders at the moment. Start by creating a new parcel!
        </div>
        <?php endif; ?>
    </div>
</body>

</html>