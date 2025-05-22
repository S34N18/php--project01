<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../auth/login.php");
    exit;
}

// Database connection (adjust these settings to match your database)
$host = 'localhost';
$dbname = 'your_database_name';
$username_db = 'your_db_username';
$password_db = 'your_db_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = trim($_POST['category']);
    $status = $_POST['status'];
    $author_id = $_SESSION['user_id']; // Assuming you store user_id in session
    
    // Validate inputs
    if (empty($title) || empty($content)) {
        $error_message = "Title and content are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO articles (title, content, category, status, author_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$title, $content, $category, $status, $author_id]);
            $success_message = "Article created successfully!";
            
            // Clear form data
            $title = $content = $category = '';
        } catch(PDOException $e) {
            $error_message = "Error creating article: " . $e->getMessage();
        }
    }
}

$username = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Article - My Website</title>
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
                    <a href="user_dashboard.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Dashboard
                    </a>
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
    <main class="max-w-4xl mx-auto mt-10 px-4">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Create New Article</h1>
                <a href="my_articles.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                    View My Articles
                </a>
            </div>

            <?php if ($success_message): ?>
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                    <p class="text-green-700"><?php echo htmlspecialchars($success_message); ?></p>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <p class="text-red-700"><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Article Title *</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        value="<?php echo htmlspecialchars($title ?? ''); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select 
                        id="category" 
                        name="category"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Select a category</option>
                        <option value="Technology" <?php echo (isset($category) && $category === 'Technology') ? 'selected' : ''; ?>>Technology</option>
                        <option value="Lifestyle" <?php echo (isset($category) && $category === 'Lifestyle') ? 'selected' : ''; ?>>Lifestyle</option>
                        <option value="Travel" <?php echo (isset($category) && $category === 'Travel') ? 'selected' : ''; ?>>Travel</option>
                        <option value="Food" <?php echo (isset($category) && $category === 'Food') ? 'selected' : ''; ?>>Food</option>
                        <option value="Business" <?php echo (isset($category) && $category === 'Business') ? 'selected' : ''; ?>>Business</option>
                        <option value="Health" <?php echo (isset($category) && $category === 'Health') ? 'selected' : ''; ?>>Health</option>
                        <option value="Other" <?php echo (isset($category) && $category === 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Article Content *</label>
                    <textarea 
                        id="content" 
                        name="content" 
                        rows="12"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Write your article content here..."
                        required
                    ><?php echo htmlspecialchars($content ?? ''); ?></textarea>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select 
                        id="status" 
                        name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="user_dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md text-sm font-medium">
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md text-sm font-medium"
                    >
                        Create Article
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>