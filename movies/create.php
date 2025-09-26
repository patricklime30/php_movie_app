<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    include 'database/db_connection.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $thumbnail = $_POST['thumbnail'];
        $youtube_link = $_POST['youtube_link'];

        $stmt = $conn->prepare("INSERT INTO movies (title, description, youtube_link, thumbnail) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $description, $youtube_link, $thumbnail);
        $stmt->execute();

        header('Location: index.php');
        exit;
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Movie</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
</head>

<body class="bg-gray-900 text-white p-6 min-h-screen">
    <a href="index.php" class="text-blue-400 underline">&larr; Back to Movies</a>
    
    <h1 class="text-3xl font-bold mb-6">Add Movie</h1>
    
    <form method="post" class="max-w-lg space-y-4">
        <input name="title" placeholder="Title" class="w-full p-2 rounded text-black" required />
        
        <textarea name="description" placeholder="Description" class="w-full p-2 rounded text-black" required></textarea>
        
        <input name="thumbnail" placeholder="Thumbnail URL" class="w-full p-2 rounded text-black" required />
        
        <input name="youtube_link" placeholder="YouTube iframe src URL" class="w-full p-2 rounded text-black" required />
        
        <button class="bg-green-600 px-4 py-2 rounded">Save Movie</button>
    </form>
</body>

</html>