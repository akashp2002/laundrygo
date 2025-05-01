<?php
require_once 'includes/db_connect.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password_raw = $_POST['password'];

// Server-side password validation
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password_raw)) {
    echo "<script>
        alert('Password does not meet security requirements.');
        window.location.href = 'signup.html';
    </script>";
    exit();
}

$password = password_hash($password_raw, PASSWORD_DEFAULT);

// Check if email exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<script>
        alert('Email already registered. Please use another email.');
        window.location.href = 'signup.html';
    </script>";
    exit();
}

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $password);

if ($stmt->execute()) {
    echo "<script>
        alert('Signup successful! Please login.');
        window.location.href = 'login.html';
    </script>";
} else {
    echo "<script>
        alert('Something went wrong. Please try again.');
        window.location.href = 'signup.html';
    </script>";
}
?>
