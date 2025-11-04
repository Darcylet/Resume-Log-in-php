<?php
session_start();
$conn = pg_connect("host=localhost dbname=postgres user=darcydecastro password=2904");

if (!$conn) {
    die("Database connection failed.");
}

$user_id = $_SESSION['user_id'] ?? null;

// If no user logged in, redirect
if (!$user_id) {
    header("Location: login.php");
    exit();
}

// Fetch existing portfolio data
$result = pg_query_params($conn, "SELECT * FROM portfolios WHERE user_id = $1", [$user_id]);
$portfolio = pg_fetch_assoc($result);

// Initialize empty portfolio if none found
if (!$portfolio) {
    $portfolio = [
        'name' => '',
        'age' => '',
        'address' => '',
        'email' => '',
        'number' => '',
        'skillset' => '',
        'degree' => '',
        'education' => '',
        'organizations' => ''
    ];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $age = $_POST['age'] ?? '';
    $address = $_POST['address'] ?? '';
    $email = $_POST['email'] ?? '';
    $number = $_POST['number'] ?? '';
    $skillset = $_POST['skillset'] ?? '';
    $degree = $_POST['degree'] ?? '';
    $education = $_POST['education'] ?? '';
    $organizations = $_POST['organizations'] ?? '';

    // If portfolio already exists, update
    if ($portfolio) {
        pg_query_params($conn, "
            UPDATE portfolios SET 
                name = $1, 
                age = $2, 
                address = $3, 
                email = $4, 
                number = $5, 
                skillset = $6, 
                degree = $7, 
                education = $8, 
                organizations = $9
            WHERE user_id = $10
        ", [$name, $age, $address, $email, $number, $skillset, $degree, $education, $organizations, $user_id]);
    } else {
        pg_query_params($conn, "
            INSERT INTO portfolios (user_id, name, age, address, email, number, skillset, degree, education, organizations)
            VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10)
        ", [$user_id, $name, $age, $address, $email, $number, $skillset, $degree, $education, $organizations]);
    }

    header("Location: portfolio.php");
    exit();
}

// Helper function to safely escape HTML
function safe($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="style.css">
<title>Edit Portfolio</title>
</head>
<body>

<div class="resume-container">
    <h2>Edit Your Portfolio</h2>
    <form method="POST">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?php echo safe($portfolio['name']); ?>" required><br><br>

        <label>Age:</label><br>
        <input type="number" name="age" value="<?php echo safe($portfolio['age']); ?>"><br><br>

        <label>Address:</label><br>
        <input type="text" name="address" value="<?php echo safe($portfolio['address']); ?>"><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?php echo safe($portfolio['email']); ?>"><br><br>

        <label>Number:</label><br>
        <input type="text" name="number" value="<?php echo safe($portfolio['number']); ?>"><br><br>

        <label>Skillset (comma-separated):</label><br>
        <textarea name="skillset" rows="3" cols="50"><?php echo safe($portfolio['skillset']); ?></textarea><br><br>

        <label>Degree:</label><br>
        <input type="text" name="degree" value="<?php echo safe($portfolio['degree']); ?>"><br><br>

        <label>Education Background:</label><br>
        <textarea name="education" rows="3" cols="50"><?php echo safe($portfolio['education']); ?></textarea><br><br>

        <label>Organizations:</label><br>
        <textarea name="organizations" rows="3" cols="50"><?php echo safe($portfolio['organizations']); ?></textarea><br><br>

        <button type="submit">Save Changes</button>
    </form>

    <p><a href="portfolio.php">Back to Portfolio</a></p>
</div>

</body>
</html>
