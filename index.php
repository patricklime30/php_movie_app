<?php
    session_start(); //check session
    
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    
    include 'database/db_connection.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Movies</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
</head>

<body class="bg-gray-900 text-white min-h-screen">
    
    <header class="bg-gray-800 p-4 flex justify-between items-center">
        <div>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></div>
        <nav class="space-x-4">
            <a href="movies/create.php" class="underline hover:text-green-400">Add Movie</a>
            <a href="logout.php" class="underline hover:text-red-400">Logout</a>
        </nav>
    </header>

    <main class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Movie List</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php
                $res = $conn->query("SELECT * FROM movies ORDER BY id DESC");
                while ($movie = $res->fetch_assoc()):
            ?>
                
            <div class="bg-gray-800 p-4 rounded shadow">
                <img src="<?= htmlspecialchars($movie['thumbnail']) ?>" class="w-full h-48 object-cover rounded mb-4" alt="Thumbnail" />
                
                <h2 class="text-xl font-semibold"><?= htmlspecialchars($movie['title']) ?></h2>
                
                <p class="text-gray-300"><?= substr(htmlspecialchars($movie['description']), 0, 100) ?>...</p>
                
                <div class="mt-2 flex gap-2">
                    <a href="show.php?id=<?= $movie['id'] ?>" class="text-blue-400 underline">View</a>
                    <a href="edit.php?id=<?= $movie['id'] ?>" class="text-yellow-400 underline">Edit</a>
                    <a href="delete.php?id=<?= $movie['id'] ?>" class="text-red-500 underline" onclick="return confirm('Delete movie?')">Delete</a>
                </div>
            </div>
            
            <?php endwhile; ?>
        </div>

    </main>
</body>

</html>