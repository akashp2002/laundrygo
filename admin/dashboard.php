<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {

    header("Location: ../login.html");

    exit();

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

    </style>

</head>

<body>



<header>

    <h1>Welcome, Admin <?= htmlspecialchars($_SESSION['user_name']) ?></h1>

</header>



<nav>

    <a href="view_orders.php">View All Orders</a>

    <a href="manage_users.php">Manage Users</a>

    <a href="manage_rates.php">Manage Rates</a>

    <a href="../login.html" style="float:right;">Logout</a>

</nav>



<div class="content">

    <h2>Dashboard Overview</h2>

    <p>Select an option from the menu to manage the system.</p>

</div>



</body>

</html>