<?php
session_start();
$conn = pg_connect("host=localhost dbname=postgres user=darcydecastro password=2904");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = pg_query_params($conn, "SELECT * FROM users WHERE username=$1", [$username]);
    $user = pg_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        header("Location: resume.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="auth-wrapper">
    <div class="auth-container">
      <h2>Login</h2>
      <form action="login.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
      </form>
      <p>Don’t have an account? <a href="register.php">Register</a></p>
    </div>
  </div>
</body>
</html>

