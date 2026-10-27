<?php
$name = $student_id = $email = $phone = $course = $message = "";
$errors = [];
$success_msg = "";
$csv_file = __DIR__ . "/submissions.csv";

function clean_input($data) { return trim($data);
}

function escape_output($data) {
return htmlspecialchars((string)$data, ENT_QUOTES, "UTF-8");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
$name = clean_input($_POST["name"] ?? "");
$student_id = clean_input($_POST["student_id"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = clean_input($_POST["phone"] ?? "");
$course = clean_input($_POST["course"] ?? "");
$message = clean_input($_POST["message"] ?? "");

if ($name === "") {
$errors[] = "Full Name is required.";
 
} elseif (!preg_match("/^[a-zA-Z ]+$/", $name)) {
$errors[] = "Name must contain only letters and spaces.";
}

if ($student_id === "") {
$errors[] = "Student ID is required.";
} elseif (!preg_match("/^[a-zA-Z0-9-]+$/", $student_id)) {
$errors[] = "Student ID can contain only letters, numbers and hyphens.";
}

if ($email === "") {
$errors[] = "Email address is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
$errors[] = "Enter a valid email address.";
}

if ($phone === "") {
$errors[] = "Contact number is required.";
} elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
$errors[] = "Phone number must contain exactly 10 digits.";
}
$allowed_courses = ["B.Tech CSE", "B.Tech IT", "B.Tech CE"]; if ($course === "") {
$errors[] = "Please select a course.";
} elseif (!in_array($course, $allowed_courses, true)) {
$errors[] = "Please select a valid course.";
}

if ($message === "") {
$errors[] = "Query / Remarks cannot be empty.";
}

if (empty($errors)) {
$fp = fopen($csv_file, "c+");

if ($fp === false) {
$errors[] = "Unable to open the CSV file. Check folder permissions.";
} elseif (!flock($fp, LOCK_EX)) { fclose($fp);
$errors[] = "Unable to lock the CSV file. Please try again.";
} else {
fseek($fp, 0, SEEK_END);
$is_empty = ftell($fp) === 0;
 

if ($is_empty) { fputcsv($fp, [
"Timestamp", "Full Name", "Student ID",
"Email", "Phone", "Course", "Message"
]);
}

$record = [
date("Y-m-d H:i:s"),
$name,
$student_id,
$email,
$phone,
$course,
$message
];

$written = fputcsv($fp, $record); fflush($fp);
flock($fp, LOCK_UN); fclose($fp);

if ($written !== false) {
$success_msg = "Registration submitted and saved successfully!";
$name = $student_id = $email = $phone = $course = $message = "";
} else {
$errors[] = "Unable to save the registration record.";
}
}
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Practical 7 - CHARUSAT Registration</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
<img src="charusat logo.jpg" alt="CHARUSAT Logo" class="logo"
 
onerror="this.style.display='none'">
<h1>CHARUSAT Student Portal</h1>
</header>

<nav>
<a href="index.html">Home</a>
<a href="login.html">Login</a>
<a href="dashboard-student.html">Student Dashboard</a>
<a href="dashboard-admin.html">Admin Dashboard</a>
<a href="profile.html">Profile</a>
<a href="courses.html">Courses</a>
<a href="registration.php" class="active">Registration</a>
<a href="results.html">Grades</a>
<a href="announcements.html">Announcements</a>
<a href="feedback.html">Feedback</a>
<a href="logout.html">Logout</a>
</nav>

<main>
<h2>Practical 7: Course Registration &amp; Feedback Form</h2>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
<strong>Please correct these errors:</strong>
<ul>
<?php foreach ($errors as $error): ?>
<li><?= escape_output($error) ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<?php if ($success_msg !== ""): ?>
<div class="alert alert-success">
<strong>Success!</strong> <?= escape_output($success_msg) ?>
</div>
<?php endif; ?>

<form action="registration.php" method="POST" novalidate>
<div class="form-group">
<label for="name">Student Full Name:</label>
<input type="text" id="name" name="name" value="<?= escape_output($name) ?>">
</div>
 
<div class="form-group">
<label for="student_id">Student ID / Roll No:</label>
<input type="text" id="student_id" name="student_id" value="<?= escape_output($student_id) ?>">
</div>

<div class="form-group">
<label for="email">CHARUSAT / Personal Email:</label>
<input type="email" id="email" name="email" value="<?= escape_output($email) ?>">
</div>

<div class="form-group">
<label for="phone">Contact Number (10 Digits):</label>
<input type="text" id="phone" name="phone" value="<?= escape_output($phone) ?>">
</div>

<div class="form-group">
<label for="course">Select Course:</label>
<select id="course" name="course">
<option value="">-- Choose Course --</option>
<option value="B.Tech CSE" <?= $course === "B.Tech CSE" ? "selected" : "" ?>>B.Tech Computer Science &amp; Engineering</option>
<option value="B.Tech IT" <?= $course === "B.Tech IT" ? "selected" : "" ?>>B.Tech Information Technology</option>
<option value="B.Tech CE" <?= $course === "B.Tech CE" ? "selected" : "" ?>>B.Tech
Computer Engineering</option>
</select>
</div>

<div class="form-group">
<label for="message">Query / Remarks:</label>
<textarea id="message" name="message" rows="3"><?= escape_output($message) ?>
</textarea>
</div>

<button type="submit" class="btn">Submit Registration</button>
</form>

<?php if (is_file($csv_file) && is_readable($csv_file)): ?>
<h2 class="table-heading">Stored Submissions (CSV Data View)</h2>
<div class="table-container">
<table>
<?php
 
$fp = fopen($csv_file, "r");

if ($fp !== false) {
$row_number = 0;

while (($row = fgetcsv($fp)) !== false) { echo "<tr>";

foreach ($row as $cell) {
$tag = $row_number === 0 ? "th" : "td"; echo "<" . $tag . ">" .
escape_output($cell) . "</" . $tag . ">";
}

echo "</tr>";
$row_number++;
}

fclose($fp);
}
?>
</table>
</div>
<?php endif; ?>
</main>

<footer>
<p>&copy; 2026 CHARUSAT University. All rights reserved.</p>
</footer>

</body>
</html>
