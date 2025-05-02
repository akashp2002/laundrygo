<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['last_order_id']) || !isset($_SESSION['payment_method'])) {
    header("Location: home.php");
    exit();
}

$payment_method = $_SESSION['payment_method'];
$order_amount = $_SESSION['order_amount'];
$order_id = $_SESSION['last_order_id'];

require_once '../includes/db_connect.php';

// Block online payment temporarily
if ($payment_method === 'online') {
    echo "<script>alert('Online payment is not available right now. Please choose Cash on Delivery.'); window.location.href = 'home.php';</script>";
    exit();
}

// If COD, set payment_status = 'pending' (only if not already set)
$update = $conn->prepare("UPDATE orders SET payment_status = 'pending' WHERE id = ?");
$update->bind_param("i", $order_id);
$update->execute();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation - LaundryGo</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f4f4f4;
            text-align: center;
            padding-top: 50px;
        }
        .box {
            background: white;
            max-width: 400px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2a2a72;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        a {
            color: #2a2a72;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<a href="order_status.php" style="display: inline-block; margin: 10px 0; padding: 10px 15px; background: #2a2a72; color: white; text-decoration: none; border-radius: 5px;">View Order Status & History</a>

<div class="box">
    <h2>Order Confirmed</h2>
    <p>Order ID: <strong>#<?php echo $order_id; ?></strong></p>
    <p>Payment Method: <strong><?php echo strtoupper($payment_method); ?></strong></p>
    <p>Total Amount: <strong>₹<?php echo number_format($order_amount, 2); ?></strong></p>
    <p class="success">Your order has been successfully placed and is awaiting payment (Cash on Delivery).</p>
    <a href="home.php">← Back to Home</a>
</div>

</body>
</html>
