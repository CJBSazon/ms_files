<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact_no = $_POST['contact_no'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if the username already exists in the database
    $checkStmtUsername = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $checkStmtUsername->execute([$username]);
    if ($checkStmtUsername->rowCount() > 0) {
        $_SESSION['register_error'] = "The username is already taken. Please choose a different username.";
        header("Location: index.php");
        exit();
    }

    // Check if the email already exists in the database
    $checkStmtEmail = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $checkStmtEmail->execute([$email]);
    if ($checkStmtEmail->rowCount() > 0) {
        $_SESSION['register_error'] = "The email address is already registered. Please use a different email.";
        header("Location: index.php");
        exit();
    }

    // Insert user into the database if both username and email are unique
    $stmt = $pdo->prepare("INSERT INTO users (username, email, contact_no, address, password) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$username, $email, $contact_no, $address, $password])) {
        $user_id = $pdo->lastInsertId();

        // Set session variables for logged-in user
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'user';
        $_SESSION['profile_pic'] = 'default-profile.png';

        header("Location: index.php");
        exit();
    } else {
        $_SESSION['register_error'] = "Could not register user. Please try again.";
        header("Location: index.php");
        exit();
    }
}
?>
