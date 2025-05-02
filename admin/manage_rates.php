<?php
include '../includes/db_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_type = htmlspecialchars($_POST['item_type']);
    $cleaning_type = htmlspecialchars($_POST['cleaning_type']);
    $rate = $_POST['rate'];

    // Check if rate already exists
    $stmt = $conn->prepare("SELECT id FROM cleaning_rates WHERE item_type = ? AND cleaning_type = ?");
    $stmt->bind_param("ss", $item_type, $cleaning_type);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Update existing rate
        $stmt = $conn->prepare("UPDATE cleaning_rates SET rate = ? WHERE item_type = ? AND cleaning_type = ?");
        $stmt->bind_param("dss", $rate, $item_type, $cleaning_type);
        $message = $stmt->execute() ? "✅ Rate updated successfully!" : "❌ Error updating rate: " . $stmt->error;
    } else {
        // Insert new rate
        $stmt = $conn->prepare("INSERT INTO cleaning_rates (item_type, cleaning_type, rate) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $item_type, $cleaning_type, $rate);
        $message = $stmt->execute() ? "✅ New rate added successfully!" : "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Cleaning Rates</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        label {
            display: block;
            margin-top: 15px;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #2a2a72;
            color: white;
            font-weight: bold;
            margin-top: 20px;
        }
        .msg {
            margin-top: 20px;
            padding: 10px;
            border-radius: 6px;
        }
        .success { background-color: #e0f8e9; color: #2e7d32; }
        .error { background-color: #ffe0e0; color: #c62828; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Cleaning Rates</h2>

        <?php if ($message): ?>
            <div class="msg <?php echo strpos($message, 'Error') === false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Item Type:</label>
            <select name="item_type" required>
                <option value="">-- Select Item --</option>
                <option value="Shoe">Shoe</option>
                <option value="Dress">Dress</option>
            </select>

            <label>Cleaning Type:</label>
            <select name="cleaning_type" required>
                <option value="">-- Select Cleaning Type --</option>
                <option value="Dry Clean">Dry Clean</option>
                <option value="Wet Wash">Wet Wash</option>
                <option value="Polish">Polish</option>
                <option value="Premium Wash">Premium Wash</option>
            </select>

            <label>Rate (₹):</label>
            <input type="number" name="rate" step="0.01" min="0" required>

            <button type="submit">Save Rate</button>
        </form>
    </div>
</body>
</html>
