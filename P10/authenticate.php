<?php

session_start(); require_once "db.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
header("Location: login.php"); exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

// Backend validation
if ($email === "" || $password === "") {
header("Location: login.php?error=All fields are required."); exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
header("Location: login.php?error=Invalid email address."); exit;
}

// Find user
$stmt = $conn->prepare(
"SELECT id, username, email, password, role FROM users
WHERE email = ?"
 
);

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result(); if ($result->num_rows !== 1) {
$stmt->close();
$conn->close();

header("Location: login.php?error=Invalid email or password."); exit;
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user["password"])) {

$stmt->close();
$conn->close();

header("Location: login.php?error=Invalid email or password."); exit;
}

// Regenerate session ID after successful login session_regenerate_id(true);

// Store user information in session
$_SESSION["user_id"] = $user["id"];
$_SESSION["username"] = $user["username"];
$_SESSION["email"] = $user["email"];
$_SESSION["role"] = $user["role"];
$_SESSION["last_activity"] = time();

$stmt->close();
$conn->close();

// Redirect to common dashboard
header("Location: dashboard.php"); exit;

?>
