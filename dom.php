<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Check if user is a domain expert
if ($_SESSION['user_role'] != 'domain_expert') {
    header("Location: login.html");
    exit;
}

// Fetch domain expert info from DB
$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("SELECT name,email FROM users WHERE user_id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Domain Expert Dashboard</title>
    <link rel="stylesheet" href="path-to-your-css/scisolve-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav>
                <a href="#" class="logo">Sci<span>Solve</span></a>
                <ul class="nav-links">
                    <li><a href="#" class="active">Dashboard</a></li>
                    <li><a href="problem.html">Challenges</a></li>
                    <li><a href="solution.html">Solutions</a></li>
                    <li><a href="#">Mentorship</a></li>
                    <li><a href="#">Analytics</a></li>
                </ul>
                <div class="auth-buttons">
                    <span>Welcome, <?php echo htmlspecialchars($user['name']); ?></span>
                    <a href="logout.php" class="btn btn-signup">Log Out</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main content -->
    <?php include 'domain-dashboard-content.html'; ?>
    <!-- Paste the rest of your HTML content (main + footer + scripts) here -->
</body>
</html>
