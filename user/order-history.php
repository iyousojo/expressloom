<?php 
include_once 'sidebar.php';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order History | ExpressLoom</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f6f9;
        display: flex;
    }

    .history-container {
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
    <div class="history-container">
        <h2><i class="fas fa-history"></i> Order History</h2>
        <?php 
        // Simulate fetching order history
        $orders = [
            ['id' => 1, 'date' => '2023-10-01', 'status' => 'Delivered', 'amount' => 50.00],
            ['id' => 2, 'date' => '2023-09-25', 'status' => 'In Transit', 'amount' => 30.00],
        ];
        ?>
        <?php if (!empty($orders)): ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['date']); ?></td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                    <td>$<?php echo htmlspecialchars(number_format($order['amount'], 2)); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="alert">
            <i class="fas fa-info-circle"></i> You have no order history at the moment.
        </div>
        <?php endif; ?>
    </div>
</body>

</html>