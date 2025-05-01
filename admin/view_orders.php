<?php
session_start();
require_once '../includes/db_connect.php';

// Access control: allow only admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html?error=Access Denied");
    exit();
}

// Handle status update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['order_status'];
    $admin_id = $_SESSION['user_id'];

    $valid_statuses = ['Pending', 'In Progress', 'Ready', 'Completed', 'Cancelled'];
    if (in_array($new_status, $valid_statuses)) {
        // Fetch current status
        $stmt = $conn->prepare("SELECT order_status FROM orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->bind_result($old_status);
        if ($stmt->fetch()) {
            $stmt->close();

            // Update the order
            $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
            $stmt->bind_param("si", $new_status, $order_id);
            $stmt->execute();
            $stmt->close();

            // Insert into log
            // $stmt = $conn->prepare("INSERT INTO orders (order_id, changed_by, old_status, new_status) VALUES (?, ?, ?, ?)");
            // $stmt->bind_param("iiss", $order_id, $admin_id, $old_status, $new_status);
            // $stmt->execute();
            // $stmt->close();
        } else {
            $stmt->close();
        }
    }
}

// Fetch all orders with user and item info
$sql = "
SELECT 
    o.id AS order_id,
    u.name AS customer_name,
    u.email,
    c.item_type,
    o.address,
    o.payment_method,
    o.total_amount,
    o.payment_status,
    o.order_status,
    o.created_at
FROM orders o
JOIN users u ON o.user_id = u.id
JOIN cleaning_items c ON o.cleaning_item_id = c.id
ORDER BY o.created_at DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - View Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 20px;
        }
        h2 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        select {
            padding: 4px;
        }
        button {
            padding: 4px 10px;
            background-color: #4285f4;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #3367d6;
        }
        form {
            display: flex;
            gap: 6px;
            align-items: center;
        }
    </style>
</head>
<body>

<h2>🧾 All Orders</h2>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Item</th>
            <th>Address</th>
            <th>Payment</th>
            <th>Total (INR)</th>
            <th>Order Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $row['order_id'] ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['item_type']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td><?= $row['payment_method'] ?> (<?= $row['payment_status'] ?>)</td>
                    <td><?= number_format($row['total_amount'], 2) ?></td>
                    <td>
                        <form method="POST" action="view_orders.php">
                            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                            <select name="order_status">
                                <?php
                                $statuses = ['Pending', 'In Progress', 'Ready', 'Completed', 'Cancelled'];
                                foreach ($statuses as $status) {
                                    $selected = ($row['order_status'] === $status) ? 'selected' : '';
                                    echo "<option value=\"$status\" $selected>$status</option>";
                                }
                                ?>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                    <td><?= date("d M Y", strtotime($row['created_at'])) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="9">No orders found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>