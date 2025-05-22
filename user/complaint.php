<?php include 'sidebar.php'; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Complaint | ExpressLoom</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f6f9;
        display: flex;
    }

    .complaint-container {
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

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        color: #333;
    }

    input[type="text"],
    textarea {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    textarea {
        resize: vertical;
        height: 100px;
    }

    button.submit-btn {
        width: 100%;
        padding: 12px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
    }

    button.submit-btn:hover {
        background-color: #0056b3;
    }

    .message {
        text-align: center;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .message.error {
        color: red;
    }

    .message.success {
        color: green;
    }
    </style>
</head>

<body>
    <div class="complaint-container">
        <h2><i class="fas fa-envelope"></i> Submit a Complaint</h2>
        <?php 
        // Simulate success or error messages
        $success = ''; // Replace with dynamic success message if needed
        $error = ''; // Replace with dynamic error message if needed
        ?>
        <?php if (!empty($error)): ?>
        <p class="message error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
        <p class="message success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form action="submit-complaint.php" method="post">
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" name="subject" id="subject" placeholder="Enter the subject of your complaint"
                    required>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea name="message" id="message" placeholder="Describe your issue in detail" required></textarea>
            </div>
            <button type="submit" class="submit-btn"><i class="fas fa-paper-plane"></i> Submit</button>
        </form>
    </div>
</body>

</html>