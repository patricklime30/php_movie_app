<?php
    session_start();
  
    if (isset($_SESSION['user_id'])) {
        // User is already logged in, redirect away from register page
        header('Location: index.php');
        exit;
    }

    include 'database/db_connection.php';

    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $email = $_POST['email'];
        $name = $_POST['name'];
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];

        if ($password !== $password_confirm) {
            $message = "Passwords do not match!";
        } 
        else {
            // Check if email exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            
            $stmt->store_result(); //return no object just to calculate num of rows
            
            if ($stmt->num_rows > 0) {
                $message = "Email already taken!";
            } 
            else {
                // Insert user
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (email, name, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $email, $name, $password_hash);
                $stmt->execute();
                
                header('Location: login.php');
                exit;
            }
        }
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
</head>

<body class="bg-gray-900 text-white flex justify-center items-center min-h-screen">
    <form method="post" class="bg-gray-800 p-8 rounded max-w-sm w-full space-y-4">
        <h1 class="text-2xl font-bold">Register</h1>
        
        <?php if ($message): ?>
            <div class="bg-red-600 p-2 rounded"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <input name="email" type="email" placeholder="Email" class="w-full p-2 rounded text-black" required />
        
        <input name="name" type="text" placeholder="Your Name" class="w-full p-2 rounded text-black" required />
        
        <input name="password" type="password" placeholder="Password" class="w-full p-2 rounded text-black" required />
        
        <input name="password_confirm" type="password" placeholder="Confirm Password" class="w-full p-2 rounded text-black" required />
        
        <button class="bg-green-600 px-4 py-2 rounded w-full">Register</button>
        
        <p class="text-gray-400 text-sm">Already have an account? 
            <a href="login.php" class="text-blue-400 underline">Login</a>
        </p>
    </form>
</body>

</html>