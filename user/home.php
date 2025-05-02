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
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(to right, #f0f4f8, #d9e2ec);
        margin: 0;
        padding: 0;
    }
    header, footer {
        background: #1a237e;
        color: white;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    .logout {
        position: absolute;
        right: 20px;
        top: 20px;
    }
    .logout a {
        color: #ffffff;
        text-decoration: none;
        font-weight: bold;
        background-color: #e53935;
        padding: 8px 12px;
        border-radius: 6px;
        transition: background 0.3s ease;
    }
    .logout a:hover {
        background-color: #c62828;
    }
    .container {
        padding: 40px 20px;
        max-width: 800px;
        margin: 0 auto;
    }
    h2 {
        color: #2c3e50;
        margin-bottom: 10px;
    }
    p {
        color: #555;
        margin-bottom: 25px;
    }
    form {
        background: #ffffff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }
    label {
        display: block;
        font-weight: bold;
        margin-top: 15px;
        margin-bottom: 5px;
        color: #333;
    }
    input[type="text"],
    input[type="file"],
    textarea,
    select {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
        font-size: 14px;
        background-color: #f9f9f9;
        transition: border 0.3s;
    }
    input[type="text"]:focus,
    textarea:focus,
    select:focus {
        border-color: #1a237e;
        outline: none;
        background-color: #fff;
    }
    input[type="submit"] {
        margin-top: 20px;
        width: 100%;
        padding: 12px;
        background: #1a237e;
        color: white;
        font-size: 16px;
        font-weight: bold;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.3s;
    }
    input[type="submit"]:hover {
        background-color: #303f9f;
    }
    footer {
        margin-top: 40px;
        font-size: 14px;
        background: #1a237e;
    }
    
</style>

</head>
<body>

<header>
<h1><a href="../home-html/index.html" style="color: white; text-decoration: none;">LaundryGo</a></h1>
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