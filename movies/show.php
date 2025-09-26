<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    include 'database/db_connection.php';

    $movie_id = (int)($_GET['id'] ?? 0);

    if (!$movie_id) {
        die('Invalid movie ID');
    }

    // Fetch movie
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->bind_param('i', $movie_id);
    $stmt->execute();
    
    $movie = $stmt->get_result()->fetch_assoc();
    if (!$movie) {
        die('Movie not found');
    }

    $user_id = $_SESSION['user_id'];

    // Check if favorite
    $stmtFav = $conn->prepare("SELECT * FROM favorites WHERE user_id = ? AND movie_id = ?");
    $stmtFav->bind_param('ii', $user_id, $movie_id);
    $stmtFav->execute();

    $stmtFav->store_result();
    $isFavorite = $stmtFav->num_rows > 0;

    // Handle favorite toggle via GET action (simple approach)
    if (isset($_GET['action']) && ($_GET['action'] === 'toggle_favorite')) {
        if ($isFavorite) {
            // Remove favorite
            $stmtDel = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND movie_id = ?");
            $stmtDel->bind_param('ii', $user_id, $movie_id);
            $stmtDel->execute();
        } else {
            // Add favorite
            $stmtAdd = $conn->prepare("INSERT INTO favorites (user_id, movie_id) VALUES (?, ?)");
            $stmtAdd->bind_param('ii', $user_id, $movie_id);
            $stmtAdd->execute();
        }
        
        header("Location: show.php?id=$movie_id");
        exit;
    }

    // Handle new comment POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['comment'])) {
        $comment = trim($_POST['comment']);
        
        $stmtC = $conn->prepare("INSERT INTO comments (user_id, movie_id, comment) VALUES (?, ?, ?)");
        $stmtC->bind_param('iis', $user_id, $movie_id, $comment);
        $stmtC->execute();
        
        header("Location: show.php?id=$movie_id");
        exit;
    }

    // Fetch comments
    $stmtComments = $conn->prepare("SELECT comments.comment, comments.created_at, users.username 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE comments.movie_id = ? ORDER BY comments.created_at DESC");
    
    $stmtComments->bind_param('i', $movie_id);
    $stmtComments->execute();
    
    $comments = $stmtComments->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($movie['title']) ?></title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
</head>

<body class="bg-gray-900 text-white min-h-screen p-6">
    <a href="index.php" class="text-blue-400 underline mb-4 inline-block">&larr; Back to Movies</a>
    
    <h1 class="text-4xl font-bold mb-4"><?= htmlspecialchars($movie['title']) ?></h1>
    <p class="mb-6"><?= nl2br(htmlspecialchars($movie['description'])) ?></p>

    <!-- Embedded YouTube iframe -->
    <div class="mb-6 aspect-w-16 aspect-h-9">
        <iframe src="<?= htmlspecialchars($movie['youtube_link']) ?>" frameborder="0" allowfullscreen class="w-full h-full rounded"></iframe>
    </div>

    <!-- Favorite toggle -->
    <a href="show.php?id=<?= $movie_id ?>&action=toggle_favorite" 
       class="inline-block mb-6 px-4 py-2 rounded <?= $isFavorite ? 'bg-red-600' : 'bg-green-600' ?>">
       <?= $isFavorite ? 'Remove from Favorites' : 'Add to Favorites' ?>
    </a>

    <!-- Comments -->
    <section>
        <h2 class="text-2xl font-semibold mb-4">Comments</h2>

        <form method="post" class="mb-6">
            <textarea name="comment" required placeholder="Add your comment..." class="w-full p-2 rounded text-black"></textarea>
            <button type="submit" class="mt-2 px-4 py-2 bg-blue-600 rounded">Submit</button>
        </form>

        <div class="space-y-4 max-w-2xl">
            <?php while ($comment = $comments->fetch_assoc()): ?>
                <div class="bg-gray-800 p-4 rounded">
                    <p><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
                    <div class="text-sm text-gray-400 mt-2">
                        — <?= htmlspecialchars($comment['username']) ?> at <?= $comment['created_at'] ?>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php if ($comments->num_rows === 0): ?>
                <p class="text-gray-500">No comments yet.</p>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>