<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = pg_connect("host=localhost dbname=postgres user=darcydecastro password=2904");
$name = "Ayelet D'arcy C. De Castro";
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
        <h1><?php echo $name; ?></h1>
        <p class="subtitle">Bachelor of Science in Computer Science Student</p>
        <p><a href="logout.php">Logout</a></p>
    </div>

    <?php
    if (!$conn) {
        echo "<p>Connection failed.</p>";
    } else {
        $result2 = pg_query($conn, "SELECT * FROM person");
        while ($row = pg_fetch_assoc($result2)) {
            echo "<div class='section info'>";
            echo "<h2>Personal Information</h2>";
            echo "<p><strong>Name:</strong> " . $row['name'] . "</p>";
            echo "<p><strong>Age:</strong> " . $row['age'] . "</p>";
            echo "<p><strong>Address:</strong> " . $row['address'] . "</p>";
            echo "<p><strong>Email:</strong> " . $row['email'] . "</p>";
            echo "<p><strong>Number:</strong> " . $row['number'] . "</p>";
            echo "</div>";

            echo "<div class='section skills'>";
            echo "<h2>Skillsets</h2>";
            echo "<ul>";
            $skills = explode(",", $row['skillset']); 
            foreach ($skills as $skill) {
                echo "<li>" . trim($skill) . "</li>";
            }
            echo "</ul>";
            echo "</div>";
        }
    }

    echo "<div class='section edu'>";
    echo "<h2>Education</h2>";
    echo "<p><strong>Bachelor of Science in Computer Science</strong><br>Batangas State University TNEU<br>(2023 – Present)</p>";
    echo "</div>";

    echo "<div class='section org'>";
    echo "<h2>Organization Membership</h2>";
    echo "<ul>";
    echo "<li><strong>CICS Student Council (CICS-SC)</strong><br>Technical Committee | Videography & Editing<br>2025 – Present</li>";
    echo "<li><strong>ACCESS</strong><br>Technical Committee | Graphics<br>2025 – Present</li>";
    echo "</ul>";
    echo "</div>";
    ?>
</div>

</body>
</html>
