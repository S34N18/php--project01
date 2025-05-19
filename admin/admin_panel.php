<?php
session_start();
include './conn.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Define variables to store messages
$message = '';
$errors = [];
$users = [];

// Fetch all users
try {
    $sql = "SELECT id, username, email, role, created_at FROM users ORDER BY id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $errors[] = "Error fetching users: " . $e->getMessage();
}

// Handle role change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id']) && isset($_POST['role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];
    
    // Validate role
    if ($new_role !== 'user' && $new_role !== 'admin') {
        $errors[] = "Invalid role specified";
    } else {
        try {
            $sql = "UPDATE users SET role = :role WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':role', $new_role);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            
            $message = "User role updated successfully!";
            
            // Refresh user list
            $sql = "SELECT id, username, email, role, created_at FROM users ORDER BY id ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            $errors[] = "Error updating user role: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="dashboard.php" class="text-xl font-bold text-gray-800">Admin Panel</a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">
                        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded ml-2">Admin</span>
                    </span>
                    <a href="dashboard.php" class="text-blue-600 hover:text-blue-800">
                        Dashboard
                    </a>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto mt-10 px-4 pb-10">
        <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
                    <a href="create_user.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Create New User
                    </a>
                </div>
            
            <?php if (!empty($message)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <p><?php echo $message; ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <ul class="mt-1 list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg overflow-hidden">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="py-3 px-4 text-left">ID</th>
                            <th class="py-3 px-4 text-left">Username</th>
                            <th class="py-3 px-4 text-left">Email</th>
                            <th class="py-3 px-4 text-left">Role</th>
                            <th class="py-3 px-4 text-left">Created At</th>
                            <th class="py-3 px-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($user['id']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($user['username']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="py-3 px-4">
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">Admin</span>
                                    <?php else: ?>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">User</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($user['created_at']); ?></td>
                                <td class="py-3 px-4">
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="inline-flex">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <select name="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-1.5">
                                            <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                        <button type="submit" class="ml-2 bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1.5 rounded-md">
                                            Update
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-500">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>