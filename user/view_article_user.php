<?php
session_start();


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

$article_id = $_GET['id'] ?? null;
$username = $_SESSION['username'] ?? 'user';

// Validate article ID
if (!$article_id || !is_numeric($article_id)) {
    $_SESSION['error_message'] = "Invalid article ID.";
    header("Location: my_articles.php");
    exit;
}

// Load article with author information
try {
    $stmt = $pdo->prepare("SELECT a.*, u.username as author_name, u.email as author_email 
                           FROM articles a 
                           JOIN users u ON a.author_id = u.id 
                           WHERE a.id = ?");
    $stmt->execute([$article_id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$article) {
        $_SESSION['error_message'] = "Article not found.";
        header("Location: my_articles.php");
        exit;
    }
} catch(PDOException $e) {
    $_SESSION['error_message'] = "Error loading article: " . $e->getMessage();
    header("Location: my_articles.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Article - Admin Dashboard</title>
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
                    <a href="admin_dashboard.php" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Admin Dashboard
                    </a>
                    <a href="user_dashboard.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        User View
                    </a>
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
                <h1 class="text-2xl font-bold text-gray-800">View Article (Admin)</h1>
                <a href="manage_articles.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Back to Articles
                </a>
            </div>

            <!-- Admin Notice -->
            <div class="bg-purple-50 border-l-4 border-purple-400 p-4 mb-6">
                <p class="text-purple-700">
                    <strong>Admin View:</strong> You are viewing this article as an administrator. This is a read-only view.
                </p>
            </div>

            <!-- Article Metadata -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Author Information</h3>
                        <p class="text-gray-900"><?php echo htmlspecialchars($article['author_name']); ?></p>
                        <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($article['author_email']); ?></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Article Details</h3>
                        <div class="space-y-1">
                            <p class="text-sm">
                                <span class="font-medium">Status:</span>
                                <?php if ($article['status'] === 'published'): ?>
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Published
                                    </span>
                                <?php else: ?>
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Draft
                                    </span>
                                <?php endif; ?>
                            </p>
                            <p class="text-sm">
                                <span class="font-medium">Category:</span>
                                <?php if ($article['category']): ?>
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?php echo htmlspecialchars($article['category']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="ml-2 text-gray-400">No category</span>
                                <?php endif; ?>
                            </p>
                            <p class="text-sm">
                                <span class="font-medium">Created:</span>
                                <?php echo date('F j, Y \a\t g:i A', strtotime($article['created_at'])); ?>
                            </p>
                            <?php if ($article['updated_at'] && $article['updated_at'] !== $article['created_at']): ?>
                                <p class="text-sm">
                                    <span class="font-medium">Updated:</span>
                                    <?php echo date('F j, Y \a\t g:i A', strtotime($article['updated_at'])); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Article Content -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-sm font-medium text-gray-700 mb-3">Article Title</h2>
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h1 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($article['title']); ?></h1>
                    </div>
                </div>

                <div>
                    <h2 class="text-sm font-medium text-gray-700 mb-3">Article Content</h2>
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="prose max-w-none">
                            <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Article Statistics -->
            <div class="mt-8 bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Article Statistics</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium">Word Count:</span>
                        <span class="ml-2"><?php echo str_word_count($article['content']); ?> words</span>
                    </div>
                    <div>
                        <span class="font-medium">Character Count:</span>
                        <span class="ml-2"><?php echo strlen($article['content']); ?> characters</span>
                    </div>
                    <div>
                        <span class="font-medium">Article ID:</span>
                        <span class="ml-2">#<?php echo $article['id']; ?></span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 mt-6">
                <a href="./my_articles.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md text-sm font-medium">
                    Back to Articles
                </a>
                <button 
                    onclick="window.print()" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md text-sm font-medium"
                >
                    Print Article
                </button>
            </div>
        </div>
    </main>

    <!-- Print Styles -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            nav {
                display: none !important;
            }
            body {
                background: white !important;
            }
        }
    </style>
</body>
</html>