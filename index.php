<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // Redirect based on role
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/admin_dashboard.php");
    } else {
        header("Location: user/user_dashboard.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - My Website</title>
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
                    <a href="auth/login.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Login
                    </a>
                    <a href="auth/register.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Register
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto mt-20 px-4">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-800 mb-6">Welcome to My Website</h1>
            <p class="text-xl text-gray-600 mb-8">Please login or register to access your dashboard</p>
            
            <div class="space-x-4">
                <a href="auth/login.php" class="inline-block bg-blue-600 text-white font-semibold py-3 px-6 rounded-md hover:bg-blue-700 transition duration-200">
                    Login
                </a>
                <a href="auth/register.php" class="inline-block bg-green-600 text-white font-semibold py-3 px-6 rounded-md hover:bg-green-700 transition duration-200">
                    Register
                </a>
            </div>
        </div>
        
        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">User Dashboard</h3>
                <p class="text-gray-600">Access your personal dashboard to manage your content and settings.</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Admin Panel</h3>
                <p class="text-gray-600">Administrators can manage users and system settings.</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibant text-gray-800 mb-2">Secure Access</h3>
                <p class="text-gray-600">Your data is protected with secure authentication and authorization.</p>
            </div>
        </div>
    </main>
</body>
</html>