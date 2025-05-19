<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
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
    <title>Dashboard - My Website</title>
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
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Welcome to Your Dashboard</h1>
            
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <p class="text-blue-700">
                    You have successfully logged in as a <?php echo htmlspecialchars($role); ?>.
                </p>
            </div>
            
            <div class="space-y-4">
                <p class="text-gray-700">
                    This is a sample dashboard page for demonstration purposes.
                </p>
                
                <?php if ($role === 'admin'): ?>
                <!-- Admin-only content -->
                <div class="mt-8 bg-purple-50 rounded-lg p-6 border border-purple-100">
                    <h2 class="text-xl font-semibold text-purple-800 mb-4">Admin Panel</h2>
                    <p class="text-purple-700 mb-4">As an administrator, you have access to these additional features:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>