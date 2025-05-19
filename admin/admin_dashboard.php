<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Get user information
$username = $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - My Website</title>
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
                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded ml-2">Admin</span>
                    </span>
                    <a href="user_dashboard.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        User View
                    </a>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto mt-10 px-4">
        <div class="bg-white rounded-xl shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>
            
            <div class="bg-purple-50 border-l-4 border-purple-400 p-4 mb-6">
                <p class="text-purple-700">
                    You are logged in as an administrator. You have access to all website management features.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <a href="admin_panel.php" class="block p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">User Management</h3>
                    <p class="text-gray-600">View, edit, and manage user accounts</p>
                </a>
                <a href="#" class="block p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Site Settings</h3>
                    <p class="text-gray-600">Configure website settings and preferences</p>
                </a>
                <a href="create_user.php" class="block p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Create User</h3>
                    <p class="text-gray-600">Add new users to the system</p>
                </a>
                <a href="#" class="block p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Analytics</h3>
                    <p class="text-gray-600">View site statistics and user activity</p>
                </a>
            </div>

            <div class="mt-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Articles Management</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="manage_articles.php" class="block p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">Manage Articles</h3>
                        <p class="text-gray-600">View, edit, and delete all user articles</p>
                    </a>
                    <a href="create_article.php" class="block p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">Create Article</h3>
                        <p class="text-gray-600">Create new articles for the website</p>
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>