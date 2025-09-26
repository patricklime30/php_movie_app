<?php
    session_start();
    
    if (isset($_SESSION['user_id'])) {
        // User is already logged in, redirect away from register page
        header('Location: index.php');
        exit;
    }

    include 'databasse/db_connection.php';

    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $res = $stmt->get_result(); //return objects for looping process

        if ($user = $res->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                
                header('Location: index.php');
                exit;
            } 
            else {
                $message = "Incorrect password!";
            }
        } else {
            $message = "Email not found!";
        }
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
</head>

<body class="bg-gray-900 text-white flex justify-center items-center min-h-screen">
    <form method="post" class="bg-gray-800 p-8 rounded max-w-sm w-full space-y-4">
        <h1 class="text-2xl font-bold">Login</h1>
        
        <?php if ($message): ?>
            <div class="bg-red-600 p-2 rounded"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <input name="username" type="text" placeholder="Username" class="w-full p-2 rounded text-black" required />
        
        <input name="password" type="password" placeholder="Password" class="w-full p-2 rounded text-black" required />
        
        <button class="bg-blue-600 px-4 py-2 rounded w-full">Login</button>
        
        <p class="text-gray-400 text-sm">Don't have an account? 
            <a href="register.php" class="text-green-400 underline">Register</a>
        </p>
    </form>
</body>

</html>