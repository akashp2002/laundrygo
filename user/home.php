<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];

    if ($payment_method === 'online') {
        echo "<script>alert('Online payment is coming soon. Please choose Cash on Delivery.'); window.location.href='home.php';</script>";
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $item_type = $_POST['item_type'];
    $cleaning_type = $_POST['cleaning_type'];
    $description = $_POST['description'];
    $mobile_number = $_POST['mobile_number'];
    $address = $_POST['address'];

    // Image upload
    $image_path = "";
    if ($_FILES['image']['name']) {
        $target_dir = "../images/";
        $image_path = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    // Get rate
    $rate_stmt = $conn->prepare("SELECT rate FROM cleaning_rates WHERE item_type = ? AND cleaning_type = ?");
    $rate_stmt->bind_param("ss", $item_type, $cleaning_type);
    $rate_stmt->execute();
    $rate_result = $rate_stmt->get_result();

    if ($rate_result->num_rows === 0) {
        echo "<script>alert('No rate found for this combination. Please contact support.'); window.location.href='home.php';</script>";
        exit();
    }

    $rate_row = $rate_result->fetch_assoc();
    $rate = $rate_row['rate'];

    // Insert into cleaning_items
    $stmt = $conn->prepare("INSERT INTO cleaning_items (user_id, item_type, cleaning_type, description, image_path, mobile_number) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $item_type, $cleaning_type, $description, $image_path, $mobile_number);

    if ($stmt->execute()) {
        $cleaning_item_id = $stmt->insert_id;

        // Insert into orders
        $order_stmt = $conn->prepare("INSERT INTO orders (user_id, cleaning_item_id, address, payment_method, total_amount, order_status) VALUES (?, ?, ?, ?, ?, 'Pending')");
        $order_stmt->bind_param("iissd", $user_id, $cleaning_item_id, $address, $payment_method, $rate);
        $order_stmt->execute();

        $_SESSION['last_order_id'] = $order_stmt->insert_id;
        $_SESSION['payment_method'] = $payment_method;
        $_SESSION['order_amount'] = $rate;

        header("Location: payment.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>LaundryGo - Home</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            margin: 0;
        }
        header, footer {
            background: #2a2a72;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .container {
            padding: 20px;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
        }
        input[type="submit"] {
            background: #2a2a72;
            color: white;
            border: none;
            cursor: pointer;
        }
        .logout {
            float: right;
            margin-right: 20px;
        }
    </style>
</head>
<body>

<header>
    <h1>LaundryGo</h1>
    <div class="logout"><a href="../login.html" style="color:white;">Logout</a></div>
</header>

<div class="container">
    <h2>Welcome, <?php echo $_SESSION['user_name']; ?>!</h2>
    <p>Upload your laundry request below:</p>

    <form method="POST" enctype="multipart/form-data">
        <label>Item Type:</label>
        <select name="item_type" required>
            <option value="Shoe">Shoe</option>
            <option value="Dress">Dress</option>
        </select>

        <label>Cleaning Type:</label>
        <select name="cleaning_type" required>
            <option value="Dry Clean">Dry Clean</option>
            <option value="Wet Wash">Wet Wash</option>
            <option value="Premium Wash">Premium Wash</option>
            <option value="Polish">Polish</option>
        </select>

        <label>Description:</label>
        <textarea name="description" placeholder="Add any notes..."></textarea>

        <label>Mobile Number:</label>
        <input type="text" name="mobile_number" required pattern="[0-9]{10}" title="Enter 10-digit number">

        <label>Upload Image:</label>
        <input type="file" name="image">

        <label>Address:</label>
        <textarea name="address" placeholder="Enter your pickup address" required></textarea>

        <label>Payment Method:</label>
        <select name="payment_method" required>
            <option value="cod">Cash on Delivery</option>
            <option value="online">Online Payment</option>
        </select>

        <input type="submit" value="Submit Order">
    </form>
</div>

<footer>
    &copy; 2025 LaundryGo | Pickup & Delivery Service
</footer>

</body>
</html>
