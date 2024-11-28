<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Login successful, set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['profile_pic'] = $user['profile_pic'] ?? 'default-profile.png';

        // Redirect based on user role
        if ($_SESSION['role'] == 'admin') {
            header("Location: admin.php");
            exit();
        } else {
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "Invalid username or password. Please try again.";
        header("Location: index.php");
        exit();
    }
}
?>
