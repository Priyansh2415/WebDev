<?php 
include 'db.php'; 
 
// Handle Student Creation (CREATE) 
if (isset($_POST['add_student'])) { 
    $name   = mysqli_real_escape_string($conn, $_POST['name']); 
    $email  = mysqli_real_escape_string($conn, $_POST['email']); 
    $course = mysqli_real_escape_string($conn, $_POST['course']); 
    $gender = mysqli_real_escape_string($conn, $_POST['gender']); 
 
    $insert_sql = "INSERT INTO students (name, email, course, gender)  
                   VALUES ('$name', '$email', '$course', '$gender')"; 
     
    mysqli_query($conn, $insert_sql); 
    header("Location: index.php"); 
    exit(); 
} 
 
// Handle Student Deletion (DELETE) 
if (isset($_GET['delete'])) { 
    $id = (int)$_GET['delete']; 
    $delete_sql = "DELETE FROM students WHERE id = $id"; 
    mysqli_query($conn, $delete_sql); 
    header("Location: index.php"); 
    exit(); 
} 
 
// Build Search and Filter Query (READ) 
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : ''; 
$course_filter = isset($_GET['course_filter']) ? mysqli_real_escape_string($conn, 
$_GET['course_filter']) : ''; 
 
$conditions = []; 
 
if (!empty($search)) { 
    $conditions[] = "(name LIKE '%$search%' OR email LIKE '%$search%')"; 
} 
if (!empty($course_filter)) { 
    $conditions[] = "course = '$course_filter'"; 
} 
 
$query_sql = "SELECT * FROM students"; 
if (count($conditions) > 0) { 
    $query_sql .= " WHERE " . implode(' AND ', $conditions); 
} 
$query_sql .= " ORDER BY id DESC"; 
 
$result = mysqli_query($conn, $query_sql); 
?> 
 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <title>Student Management System</title> 
    <style> 
        body { font-family: Arial, sans-serif; margin: 20px; } 
        table { width: 100%; border-collapse: collapse; margin-top: 20px; } 
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } 
        th { background-color: #f2f2f2; } 
        .form-group { margin-bottom: 10px; } 
        .form-inline { display: flex; gap: 10px; margin-bottom: 20px; } 
    </style> 
</head> 
<body> 
 
    <h2>Add New Student</h2> 
    <form method="POST" action="index.php"> 
        <div class="form-group"> 
            <input type="text" name="name" placeholder="Full Name" required> 
            <input type="email" name="email" placeholder="Email" required> 
            <select name="course" required> 
                <option value="">Select Course</option> 
                <option value="Computer Science">Computer Science</option> 
                <option value="Web Development">Web Development</option> 
                <option value="Data Science">Data Science</option> 
            </select> 
            <select name="gender" required> 
                <option value="">Select Gender</option> 
                <option value="Male">Male</option> 
                <option value="Female">Female</option> 
            </select> 
            <button type="submit" name="add_student">Add Student</button> 
        </div> 
    </form> 
 
    <hr> 
 
    <h2>Student List</h2> 
    <!-- Search and Filter Form --> 
    <form method="GET" action="index.php" class="form-inline"> 
        <input type="text" name="search" placeholder="Search by name or email" value="<?php 
echo htmlspecialchars($search); ?>"> 
        <select name="course_filter"> 
            <option value="">All Courses</option> 
            <option value="Computer Science" <?php if ($course_filter == 'Computer Science') echo 
'selected'; ?>>Computer Science</option> 
            <option value="Web Development" <?php if ($course_filter == 'Web Development') echo 
'selected'; ?>>Web Development</option> 
            <option value="Data Science" <?php if ($course_filter == 'Data Science') echo 'selected'; 
?>>Data Science</option> 
        </select> 
        <button type="submit">Filter</button> 
        <a href="index.php"><button type="button">Reset</button></a> 
    </form> 
 
    <!-- Table Displaying Results --> 
    <table> 
        <thead> 
            <tr> 
                <th>ID</th> 
                <th>Name</th> 
                <th>Email</th> 
                <th>Course</th> 
                <th>Gender</th> 
                <th>Actions</th> 
            </tr> 
        </thead> 
        <tbody> 
            <?php if (mysqli_num_rows($result) > 0): ?> 
                <?php while ($row = mysqli_fetch_assoc($result)): ?> 
                    <tr> 
                        <td><?php echo $row['id']; ?></td> 
                        <td><?php echo htmlspecialchars($row['name']); ?></td> 
                        <td><?php echo htmlspecialchars($row['email']); ?></td> 
                        <td><?php echo htmlspecialchars($row['course']); ?></td> 
                        <td><?php echo htmlspecialchars($row['gender']); ?></td> 
                        <td> 
                            <a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a> |  
                            <a href="index.php?delete=<?php echo $row['id']; ?>" onclick="return 
confirm('Delete this record?');">Delete</a> 
                        </td> 
                    </tr> 
                <?php endwhile; ?> 
            <?php else: ?> 
                <tr> 
                    <td colspan="6">No records found.</td> 
                </tr> 
            <?php endif; ?> 
        </tbody> 
    </table> 
 
</body> 
</html> 

