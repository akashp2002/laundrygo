<?php
session_start();
require_once '../includes/db_connect.php'; // Ensure your DB connection file is correct

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

// Count orders by status
$status_counts = [
    'Pending' => 0,
    'In Progress' => 0,
    'Ready' => 0,
    'Completed' => 0,
    'Cancelled' => 0
];

$sql = "SELECT order_status, COUNT(*) as count FROM orders GROUP BY order_status";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $status = $row['order_status'];
    if (isset($status_counts[$status])) {
        $status_counts[$status] = $row['count'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - LaundryGo</title>
    <style>
        body {
            font-family: Arial;
            margin: 0;
            background: #f0f2f5;
        }
        header {
            background-color: #2a2a72;
            color: white;
            padding: 15px;
            text-align: center;
        }
        nav {
            background: #333;
            overflow: hidden;
        }
        nav a {
            float: left;
            display: block;
            color: white;
            padding: 14px 20px;
            text-decoration: none;
        }
        nav a:hover {
            background: #575757;
        }
        .content {
            padding: 30px;
        }
        .status-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            min-width: 150px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .card strong {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
</header>

<nav>
    <a href="view_orders.php">View All Orders</a>
    <a href="manage_rates.php">Manage Rates</a>
    <a href="../login.html" style="float:right;">Logout</a>
</nav>

<div class="content">
    <h2>Dashboard Overview</h2>
    <p>Select an option from the menu to manage the system.</p>

    <div class="status-cards">
        <?php foreach ($status_counts as $status => $count): ?>
            <div class="card">
                <strong><?= htmlspecialchars($status) ?></strong>
                <?= $count ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>