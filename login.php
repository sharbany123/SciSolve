<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $row = $result->fetch_assoc();

        // Password verification (use password_verify if hashed)
        if($row['password_hash'] == $password || password_verify($password, $row['password_hash'])){
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];

            // Redirect based on role
            if($row['role'] == 'admin'){
                header("Location: admin.php");
            } elseif($row['role'] == 'user'){
                header("Location: challenges.php");
            } elseif($row['role'] == 'domain_expert'){
                header("Location: domain.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid password!";
            header("Location: login.html");
            exit();
        }
    } else {
        $_SESSION['error'] = "Email not found!";
        header("Location: login.html");
        exit();
    }
} else {
    header("Location: login.html");
    exit();
}
