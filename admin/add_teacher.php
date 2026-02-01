<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Teacher</title>
</head>
<body>
    <div class="container">
        <div class="left_side">
            <ul>
                <ul><a href="add_course.php">Add Course & Assign Teacher</a></ul>
                <ul><a href="add_teacher.php">Add Teacher</a></ul>
                <ul><a href="edit.php">Manage Courses & Teachers</a></ul>
                <ul><a href="manage_students.php">Manage Students</a></ul>
                <ul><a href="delete.php">Delete Data</a></ul>
                <ul><a href="../auth/logout.php">Logout</a></ul>
            </ul>
        </div>
        <div class="right_side">
            <h2>Add New Teacher</h2>
            <form action="" method="post">
                <label for="teacher_name">Teacher Name:</label><br>
                <input type="text" id="teacher_name" name="teacher_name" required><br><br>
                <label for="teacher_email">Teacher Email:</label><br>
                <input type="email" id="teacher_email" name="teacher_email" required><br><br>
                <label for="teacher_password">Password:</label><br>
                <input type="password" id="teacher_password" name="teacher_password" required><br><br>
                <button type="submit">Add Teacher</button>
            </form>
            <div class="table">
            <?php
            ?>
            </div>
    </div>
</body>
</html>