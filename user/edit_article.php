<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../auth/login.php");
    exit;
}

// Database configuration
$host = 'localhost';
$dbname = 'mywebsite';
$username_db = 'root';
$password_db = '';

// Database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$author_id = $_SESSION['user_id'];
$article_id = $_GET['id'] ?? null;
$success_message = '';
$error_message = '';

// Validate article ID
if (!$article_id || !is_numeric($article_id)) {
    $_SESSION['error_message'] = "Invalid article ID.";
    header("Location: my_articles.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    
    // Validate inputs
    if (empty($title) || empty($content)) {
        $error_message = "Title and content are required.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE articles SET title = ?, content = ?, category = ?, status = ?, updated_at = NOW() WHERE id = ? AND author_id = ?");
            $result = $stmt->execute([$title, $content, $category, $status, $article_id, $author_id]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['success_message'] = "Article updated successfully!";
                header("Location: my_articles.php");
                exit;
            } else {
                $error_message = "No changes were made or article not found.";
            }
        } catch(PDOException $e) {
            $error_message = "Error updating article: " . $e->getMessage();
        }
    }
}

// Load article to edit
try {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ? AND author_id = ?");
    $stmt->execute([$article_id, $author_id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$article) {
        $_SESSION['error_message'] = "Article not found or access denied.";
        header("Location: my_articles.php");
        exit;
    }
} catch(PDOException $e) {
    $_SESSION['error_message'] = "Error loading article: " . $e->getMessage();
    header("Location: my_articles.php");
    exit;
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
    <title>Edit Article - My Website</title>
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
                <h1 class="text-2xl font-bold text-gray-800">Edit Article</h1>
                <a href="my_articles.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Back to My Articles
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
                        value="<?php echo htmlspecialchars($article['title']); ?>"
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
                        <option value="Technology" <?php echo ($article['category'] === 'Technology') ? 'selected' : ''; ?>>Technology</option>
                        <option value="Lifestyle" <?php echo ($article['category'] === 'Lifestyle') ? 'selected' : ''; ?>>Lifestyle</option>
                        <option value="Travel" <?php echo ($article['category'] === 'Travel') ? 'selected' : ''; ?>>Travel</option>
                        <option value="Food" <?php echo ($article['category'] === 'Food') ? 'selected' : ''; ?>>Food</option>
                        <option value="Business" <?php echo ($article['category'] === 'Business') ? 'selected' : ''; ?>>Business</option>
                        <option value="Health" <?php echo ($article['category'] === 'Health') ? 'selected' : ''; ?>>Health</option>
                        <option value="Other" <?php echo ($article['category'] === 'Other') ? 'selected' : ''; ?>>Other</option>
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
                    ><?php echo htmlspecialchars($article['content']); ?></textarea>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select 
                        id="status" 
                        name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="draft" <?php echo ($article['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo ($article['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="my_articles.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md text-sm font-medium">
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md text-sm font-medium"
                    >
                        Update Article
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>