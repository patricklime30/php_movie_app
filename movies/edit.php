<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    include 'database/db_connection.php';

    $movie_id = (int)($_GET['id'] ?? 0);
    if (!$movie_id) die('Invalid movie ID');

    // Fetch movie
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->bind_param('i', $movie_id);
    $stmt->execute();
    $movie = $stmt->get_result()->fetch_assoc();
    if (!$movie) die('Movie not found');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $thumbnail = $_POST['thumbnail'];
        $youtube_link = $_POST['youtube_link'];

        $stmt = $conn->prepare("UPDATE movies SET title = ?, description = ?, thumbnail = ?, youtube_link = ? WHERE id = ?");
        $stmt->bind_param('ssssi', $title, $description, $thumbnail, $youtube_link, $movie_id);
        $stmt->execute();

        header("Location: show.php?id=$movie_id");
        exit;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Movie</title>
     <script src="https://cdn.tailwindcss.com/3.4.16"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen p-6">
    <a href="index.php" class="text-blue-400 underline mb-4 inline-block">&larr; Back to Movies</a>
    <h1 class="text-3xl font-bold mb-6">Edit Movie</h1>

    <form method="post" class="max-w-lg space-y-4">
        <input type="text" name="title" value="<?= htmlspecialchars($movie['title']) ?>" required class="w-full p-2 rounded text-black" />
        
        <textarea name="description" required class="w-full p-2 rounded text-black"><?= htmlspecialchars($movie['description']) ?></textarea>
        
        <input type="text" name="thumbnail" value="<?= htmlspecialchars($movie['thumbnail']) ?>" required class="w-full p-2 rounded text-black" />
        
        <input type="text" name="youtube_link" value="<?= htmlspecialchars($movie['youtube_link']) ?>" required class="w-full p-2 rounded text-black" />
        
        <button type="submit" class="bg-yellow-600 px-4 py-2 rounded">Save Changes</button>
    </form>
</body>
</html>
