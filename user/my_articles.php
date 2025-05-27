<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../auth/login.php");
    exit;
}

// Database connection (adjust these settings to match your database)
$host = 'localhost';
$dbname = 'mywebsite';
$username_db = 'root';
$password_db = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$success_message = '';
$error_message = '';

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $article_id = $_GET['delete'];
    $author_id = $_SESSION['user_id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ? AND author_id = ?");
        $stmt->execute([$article_id, $author_id]);
        
        if ($stmt->rowCount() > 0) {
            $success_message = "Article deleted successfully!";
        } else {
            $error_message = "Article not found or you don't have permission to delete it.";
        }
    } catch(PDOException $e) {
        $error_message = "Error deleting article: " . $e->getMessage();
    }
}

// Fetch user's articles
$author_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "SELECT id, title, category, status, created_at, updated_at FROM articles WHERE author_id = ?";
$params = [$author_id];

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($filter_status)) {
    $sql .= " AND status = ?";
    $params[] = $filter_status;
}

$sql .= " ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_message = "Error fetching articles: " . $e->getMessage();
    $articles = [];
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
    <title>My Articles - My Website</title>
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
                    <a href="./user_dashboard.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Dashboard
                    </a>
                    <?php if ($role === 'admin'): ?>
                        <a href="../admin/admin_dashboard.php" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md text-sm font-medium">
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
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">My Articles</h1>
                <a href="create_article.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Create New Article
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

            <!-- Search and Filter -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <form method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input 
                            type="text" 
                            name="search" 
                            value="<?php echo htmlspecialchars($search); ?>"
                            placeholder="Search articles by title or content..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                    <div>
                        <select 
                            name="status"
                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Status</option>
                            <option value="draft" <?php echo $filter_status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?php echo $filter_status === 'published' ? 'selected' : ''; ?>>Published</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button 
                            type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium"
                        >
                            Search
                        </button>
                        <a 
                            href="my_articles.php" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium"
                        >
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Articles List -->
            <?php if (empty($articles)): ?>
                <div class="text-center py-8">
                    <p class="text-gray-500 text-lg">No articles found.</p>
                    <a href="create_article.php" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md text-sm font-medium">
                        Create Your First Article
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($articles as $article): ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                        <?php echo htmlspecialchars($article['title']); ?>
                                    </h3>
                                    <div class="flex items-center space-x-4 text-sm text-gray-600 mb-2">
                                        <span>Category: <?php echo htmlspecialchars($article['category'] ?: 'Uncategorized'); ?></span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php echo $article['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                            <?php echo ucfirst($article['status']); ?>
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <span>Created: <?php echo date('M j, Y g:i A', strtotime($article['created_at'])); ?></span>
                                        <?php if ($article['updated_at'] !== $article['created_at']): ?>
                                            <span class="ml-4">Updated: <?php echo date('M j, Y g:i A', strtotime($article['updated_at'])); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 ml-4">
                                    <a href="./edit_article.php?id=<?php echo $article['id']; ?>" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm font-medium">
                                        Edit
                                    </a>
                                    <a href="./view_article_user.php?id=<?php echo $article['id']; ?>" 
                                       class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm font-medium">
                                        View
                                    </a>
                                    <a href="?delete=<?php echo $article['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this article?')"
                                       class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-medium">
                                        Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>