<?php
session_start();
require 'config.php'; // Database configuration

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Fetch user data
$query = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

// Function to handle profile picture update
function updateProfilePicture($pdo, $user_id)
{
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = basename($_FILES['profile_picture']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validate file extension
        if (in_array($file_ext, $allowed_extensions)) {
            $file_path = "uploads/" . uniqid() . "_" . $file_name;

            if (move_uploaded_file($file_tmp, $file_path)) {
                $update_query = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                $update_query->execute([$file_path, $user_id]);
                $_SESSION['profile_pic'] = $file_path; // Update session
                return "Profile picture updated successfully!";
            }
            return "Failed to upload the profile picture.";
        }
        return "Invalid file type. Only JPG, JPEG, PNG, or GIF files are allowed.";
    }
    return "Please select a valid profile picture.";
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_picture'])) {
    $success_message = updateProfilePicture($pdo, $user_id);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-8">
     <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-gray-700 mb-6">Update Profile Picture</h1>

        <!-- Success/Error Messages -->
        <?php if ($success_message): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $success_message ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= $error_message ?></div>
        <?php endif; ?>

        <!-- Profile Picture Update Form -->
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <img src="<?= $user['profile_picture'] ?: 'default-profile.png' ?>" alt="Profile Picture" class="w-24 h-24 rounded-full border mx-auto">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-600">Upload New Profile Picture</label>
                <input type="file" name="profile_picture" accept="image/*" required class="w-full p-2 border rounded">
            </div>
            <button type="submit" name="update_picture" class="bg-blue-500 text-white px-4 py-2 rounded">Update Picture</button>
        </form>
    

        <!-- Profile Information Update -->
        <form action="" method="POST" class="mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Update Information</h2>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-600">Contact Number</label>
                <input type="text" name="contact_no" value="<?= htmlspecialchars($user['contact_no']) ?>" pattern="\d{10,15}" required class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-600">Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" required class="w-full p-2 border rounded">
            </div>
            <button type="submit" name="update_info" class="bg-green-500 text-white px-4 py-2 rounded">Update Information</button>
        </form>

        <!-- Password Change -->
        <form action="" method="POST">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Change Password</h2>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-600">Old Password</label>
                <input type="password" name="old_password" required class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
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
