<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
$full_name = trim($_POST["full_name"] ?? "");
$email = trim($_POST["email"] ?? "");

if ($full_name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
 
exit("Please enter a valid name and email.");
}

try {
$stmt = $pdo->prepare(
"INSERT INTO rudra1 (full_name, email) VALUES (:full_name, :email)"
);

$stmt->execute([
"full_name" => $full_name, "email" => $email
]);

echo "Registration successful!";
} catch (PDOException $e) { error_log($e->getMessage());

if ($e->getCode() === "23000") {
echo "This email address is already registered.";
} else {
http_response_code(500);
echo "Registration failed: " . $e->getMessage();
}
}
} else {
echo "Please submit the registration form.";
}
?>
