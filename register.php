<?php
session_start();
$conn = pg_connect("host=localhost dbname=postgres user=darcydecastro password=2904");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];


    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $error = "Password must be at least 8 characters long, include uppercase, lowercase, and a number.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

   
        $check = pg_query_params($conn, "SELECT * FROM users WHERE username=$1", [$username]);
        if (pg_num_rows($check) > 0) {
            $error = "Username already exists!";
        } else {
          
            $result = pg_query_params($conn, "INSERT INTO users (username, password) VALUES ($1, $2)", [$username, $hashedPassword]);

            if ($result) {
      
                $userResult = pg_query_params($conn, "SELECT id FROM users WHERE username = $1", [$username]);
                $newUser = pg_fetch_assoc($userResult);
                $newUserId = $newUser['id'];

                pg_query_params($conn, "INSERT INTO portfolios (user_id, name, age, address, email, number, skillset)
                                        VALUES ($1, '', NULL, '', '', '', '')", [$newUserId]);

                header("Location: portfolio.php");
                exit();
            } else {
                $error = "Error registering user.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Register</h2>
        <?php if (!empty($error)) echo "<p class='error-message'>" . htmlspecialchars($error) . "</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
