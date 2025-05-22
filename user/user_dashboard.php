<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../auth/login.php");
    exit;
}

// Get user information
$username = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - My Website</title>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php" class="text-xl font-bold text-gray-800">My Website</a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">
                        Welcome, <?php echo htmlspecialchars($username); ?>
                        <?php if ($role === 'admin'): ?>
                            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded ml-2">Admin</span>
                        <?php endif; ?>
                    </span>
                    <?php if ($role === 'admin'): ?>
                        <a href="admin_dashboard.php" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Admin Dashboard
                        </a>
                    <?php endif; ?>
                    <a href="../auth/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto mt-10 px-4">
        <div class="bg-white rounded-xl shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Welcome to Your Dashboard</h1>
            
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <p class="text-blue-700">
                    You have successfully logged in to your user dashboard.
                </p>
            </div>
            
            <div class="space-y-4 mb-8">
                <p class="text-gray-700">
                    From this dashboard, you can manage your content and account settings.
                </p>
            </div>

            <!-- Article Management Section -->
            <div class="mt-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Your Articles</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="my_articles.php" class="block p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">My Articles</h3>
                        <p class="text-gray-600">View and manage articles you've created</p>
                    </a>
                    <a href="create_article.php" class="block p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">Create New Article</h3>
                        <p class="text-gray-600">Write and publish a new article</p>
                    </a>
                </div>
            </div>

            <!-- Account Settings Section -->
            <div class="mt-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Account Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="profile.php" class="block p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">Edit Profile</h3>
                        <p class="text-gray-600">Update your account information</p>
                    </a>
                    <a href="change_password.php" class="block p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">Change Password</h3>
                        <p class="text-gray-600">Update your account password</p>
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>