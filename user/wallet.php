<?php 
include_once 'sidebar.php';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wallet | ExpressLoom</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f6f9;
        display: flex;
    }

    .wallet-container {
        margin: 100px auto;
        padding: 30px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 800px;
        width: 100%;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #007bff;
        font-size: 28px;
    }

    .balance {
        text-align: center;
        font-size: 24px;
        margin-bottom: 20px;
        color: #333;
    }

    .actions {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }

    .actions button {
        flex: 1;
        padding: 15px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
    }

    .actions button:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>
    <div class="wallet-container">
        <h2><i class="fas fa-wallet"></i> My Wallet</h2>
        <div class="balance">
            <strong>Balance:</strong> $<?php echo number_format(100.00, 2); // Replace with dynamic balance ?>
        </div>
        <div class="actions">
            <button onclick="window.location.href='add-funds.php'">Add Funds</button>
            <button onclick="window.location.href='transaction-history.php'">Transaction History</button>
        </div>
    </div>
</body>

</html>