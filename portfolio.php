<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = pg_connect("host=localhost dbname=postgres user=darcydecastro password=2904");

if (!$conn) {
    die("Database connection failed.");
}

$user_id = $_SESSION['user_id'];

$result = pg_query_params($conn, "SELECT * FROM portfolios WHERE user_id = $1", [$user_id]);
$portfolio = pg_fetch_assoc($result);

if (!$portfolio) {
    pg_query_params($conn, "INSERT INTO portfolios (user_id, name, age, address, email, number, skillset, degree, education, organizations)
                            VALUES ($1, '', NULL, '', '', '', '', '', '', '')", [$user_id]);
    $result = pg_query_params($conn, "SELECT * FROM portfolios WHERE user_id = $1", [$user_id]);
    $portfolio = pg_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="style.css">
<title>Resume</title>
</head>

<body>

<div class="resume-container">
    <div class="header">
        <h1><?php echo htmlspecialchars($portfolio['name'] ?: 'Unnamed User'); ?></h1>
        <p class="subtitle"><?php echo htmlspecialchars($portfolio['degree'] ?: 'Bachelor of Science in Computer Science Student'); ?></p>
        <p><a href="logout.php">Logout</a> | <a href="edit_portfolio.php">Edit</a></p>
    </div>

    <div class='section info'>
        <h2>Personal Information</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($portfolio['name']); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($portfolio['age']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($portfolio['address']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($portfolio['email']); ?></p>
        <p><strong>Number:</strong> <?php echo htmlspecialchars($portfolio['number']); ?></p>
    </div>

    <div class='section skills'>
        <h2>Skillsets</h2>
        <ul>
            <?php
            $skills = array_filter(array_map('trim', explode(',', $portfolio['skillset'] ?? '')));
            foreach ($skills as $skill) {
                echo "<li>" . htmlspecialchars($skill) . "</li>";
            }
            ?>
        </ul>
    </div>

    <div class='section edu'>
        <h2>Education</h2>
        <p><?php echo nl2br(htmlspecialchars($portfolio['education'] ?: 'No education background listed.')); ?></p>
    </div>

    <div class='section org'>
        <h2>Organization Membership</h2>
        <p><?php echo nl2br(htmlspecialchars($portfolio['organizations'] ?: 'No organizations listed.')); ?></p>
    </div>
</div>

</body>
</html>
