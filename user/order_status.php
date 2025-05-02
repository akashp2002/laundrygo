<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
require_once '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];

// Latest order
$latest_sql = "SELECT id, order_status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
$latest_stmt = $conn->prepare($latest_sql);
$latest_stmt->bind_param("i", $user_id);
$latest_stmt->execute();
$latest_result = $latest_stmt->get_result();
$latest_order = $latest_result->fetch_assoc();

// All orders with details
$history_sql = "
    SELECT o.id AS order_id, o.created_at, o.order_status, o.payment_method, o.total_amount,
           ci.item_type, ci.cleaning_type
    FROM orders o
    JOIN cleaning_items ci ON o.cleaning_item_id = ci.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
";
$history_stmt = $conn->prepare($history_sql);
$history_stmt->bind_param("i", $user_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Status & History - LaundryGo</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7f8fc;
            margin: 0;
            padding: 0;
        }
        header, footer {
            background: #2a2a72;
            color: white;
            text-align: center;
            padding: 15px;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        h2, h3 {
            color: #2a2a72;
        }
        .status-box {
            background: #e3e9ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .status-box span {
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th {
            background-color: #2a2a72;
            color: white;
            padding: 10px;
        }
        td {
            padding: 10px;
            text-align: center;
        }
        .back-link {
            display: inline-block;
            margin-top: 25px;
            background: #2a2a72;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>
<body>

<header>
    <h1>LaundryGo - Order Status & History</h1>
</header>

<div class="container">
    <h2>Welcome, <?php echo $_SESSION['user_name']; ?>!</h2>

    <?php if ($latest_order): ?>
        <div class="status-box">
            <span>Latest Order Status (ID: <?php echo $latest_order['id']; ?>):</span>
            <?php echo htmlspecialchars($latest_order['order_status']); ?>  
            <br><small>Placed on: <?php echo date("d M Y, h:i A", strtotime($latest_order['created_at'])); ?></small>
        </div>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>

    <h3>Your Order History</h3>
    <?php if ($history_result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Item</th>
                <th>Cleaning</th>
                <th>Payment</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
            <?php while($row = $history_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['order_id']; ?></td>
                    <td><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>
                    <td><?php echo $row['item_type']; ?></td>
                    <td><?php echo $row['cleaning_type']; ?></td>
                    <td><?php echo strtoupper($row['payment_method']); ?></td>
                    <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
                    <td><?php echo $row['order_status']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No order history yet.</p>
    <?php endif; ?>

    <a class="back-link" href="home.php">← Back to Home</a>
</div>

<footer>
    &copy; 2025 LaundryGo | Your trusted laundry service
</footer>

</body>
</html>
