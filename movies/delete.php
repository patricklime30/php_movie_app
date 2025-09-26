<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    include 'database/db_connection.php';

    $movie_id = (int)($_GET['id'] ?? 0);
    if (!$movie_id) die('Invalid movie ID');

    $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->bind_param('i', $movie_id);
    $stmt->execute();

    header('Location: index.php');
    exit;
?>