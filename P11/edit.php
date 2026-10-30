<?php 
include 'db.php'; 
 
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0; 
 
// Fetch existing record 
$select_sql = "SELECT * FROM students WHERE id = $id"; 
$fetch_result = mysqli_query($conn, $select_sql); 
$student = mysqli_fetch_assoc($fetch_result); 
 
if (!$student) { 
    die("Student record not found."); 
} 
 
// Handle Form Update (UPDATE) 
if (isset($_POST['update_student'])) { 
    $name   = mysqli_real_escape_string($conn, $_POST['name']); 
    $email  = mysqli_real_escape_string($conn, $_POST['email']); 
    $course = mysqli_real_escape_string($conn, $_POST['course']); 
    $gender = mysqli_real_escape_string($conn, $_POST['gender']); 
 
    $update_sql = "UPDATE students  
                   SET name = '$name', email = '$email', course = '$course', gender = '$gender'  
                   WHERE id = $id"; 
 
    mysqli_query($conn, $update_sql); 
    header("Location: index.php"); 
    exit(); 
} 
?> 
 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <title>Edit Student</title> 
</head> 
<body> 
 
    <h2>Edit Student Details</h2> 
    <form method="POST" action="edit.php?id=<?php echo $id; ?>"> 
        <div> 
            <label>Name:</label><br> 
            <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); 
?>" required> 
        </div><br> 
        <div> 
            <label>Email:</label><br> 
            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); 
?>" required> 
        </div><br> 
        <div> 
            <label>Course:</label><br> 
            <select name="course" required> 
                <option value="Computer Science" <?php if ($student['course'] == 'Computer Science') 
echo 'selected'; ?>>Computer Science</option> 
                <option value="Web Development" <?php if ($student['course'] == 'Web Development') 
echo 'selected'; ?>>Web Development</option> 
                <option value="Data Science" <?php if ($student['course'] == 'Data Science') echo 
'selected'; ?>>Data Science</option> 
            </select> 
        </div><br> 
        <div> 
            <label>Gender:</label><br> 
            <select name="gender" required> 
                <option value="Male" <?php if ($student['gender'] == 'Male') echo 'selected'; 
?>>Male</option> 
                <option value="Female" <?php if ($student['gender'] == 'Female') echo 'selected'; 
?>>Female</option> 
            </select> 
        </div><br> 
        <button type="submit" name="update_student">Update</button> 
        <a href="index.php">Cancel</a> 
    </form> 
 
</body> 
</html>
