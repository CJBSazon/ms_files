<?php
session_start();
require 'config.php';  // Database configuration file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Fetch current user data
$query = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

// Handle profile picture update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_picture'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = "uploads/" . uniqid() . "_" . basename($_FILES['profile_picture']['name']);
        
        if (move_uploaded_file($file_tmp, $file_name)) {
            $update_query = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $update_query->execute([$file_name, $user_id]);
            $_SESSION['profile_pic'] = $file_name;  // Update session profile picture
            $success_message = "Profile picture updated successfully!";
        } else {
            $error_message = "Failed to upload profile picture.";
        }
    } else {
        $error_message = "Please select a valid profile picture.";
    }
}

// Handle profile information update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $contact_no = $_POST['contact_no'];
    $address = $_POST['address'];

    $update_query = $pdo->prepare("UPDATE users SET contact_no = ?, address = ? WHERE id = ?");
    $update_query->execute([$contact_no, $address, $user_id]);

    $success_message = "Profile information updated successfully!";
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify old password
    if (password_verify($old_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_query->execute([$new_password_hashed, $user_id]);
            $success_message = "Password updated successfully!";
        } else {
            $error_message = "New passwords do not match.";
        }
    } else {
        $error_message = "Old password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-gray-700 mb-4">View Profile</h2>

        <!-- Success/Error Messages -->
        <?php if ($success_message): ?>
            <p class="bg-green-100 text-green-700 p-2 rounded mb-4"><?= $success_message ?></p>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <p class="bg-red-100 text-red-700 p-2 rounded mb-4"><?= $error_message ?></p>
        <?php endif; ?>

        <!-- Profile Picture Update Form -->
        <form action="view_profile.php" method="POST" enctype="multipart/form-data" class="mb-4">
            <h3 class="font-bold text-gray-700">Profile Picture</h3>
            <div class="flex items-center space-x-4">
            <img src="<?= !empty($user['profile_picture']) ? $user['profile_picture'] : 'default-profile.png' ?>" alt="Profile Picture" class="w-16 h-16 rounded-full border">
            <input type="file" name="profile_picture" class="border p-2 rounded">
                <button type="submit" name="update_picture" class="bg-blue-500 text-white px-4 py-2 rounded">Update Picture</button>
            </div>
        </form>

        <!-- Profile Information Update Form -->
        <form action="view_profile.php" method="POST" class="mb-4">
            <h3 class="font-bold text-gray-700">Update Information</h3>
            <div class="mb-2">
                <label class="block text-sm font-semibold text-gray-600">Contact Number</label>
                <input type="text" name="contact_no" value="<?= $user['contact_no'] ?>" pattern="\d{11}" required class="w-full p-2 border rounded">
            </div>
            <div class="mb-2">
                <label class="block text-sm font-semibold text-gray-600">Address</label>
                <input type="text" name="address" value="<?= $user['address'] ?>" required class="w-full p-2 border rounded">
            </div>
            <button type="submit" name="update_info" class="bg-green-500 text-white px-4 py-2 rounded">Update Information</button>
        </form>

        <!-- Password Change Form -->
        <form action="view_profile.php" method="POST">
            <h3 class="font-bold text-gray-700">Change Password</h3>
            <div class="mb-2">
                <label class="block text-sm font-semibold text-gray-600">Old Password</label>
                <input type="password" name="old_password" required class="w-full p-2 border rounded">
            </div>
            <div class="mb-2">
                <label class="block text-sm font-semibold text-gray-600">New Password</label>
                <input type="password" name="new_password" required class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-600">Confirm New Password</label>
                <input type="password" name="confirm_password" required class="w-full p-2 border rounded">
            </div>
            <button type="submit" name="change_password" class="bg-red-500 text-white px-4 py-2 rounded">Change Password</button>
        </form>
    </div>
</body>
</html>
