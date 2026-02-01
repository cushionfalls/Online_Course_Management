<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course & Teacher</title>
</head>
<body>
    <h2>Add New Course & Assign Teacher</h2>
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
    <div class="add_course_teacher">
    <h2>Course & Teacher Form</h2>
    <form>
        <label for="course_name">Course Name:</label><br>
        <input type="text" id="course_name" name="course_name" required><br><br>
        <label for="course_code">Course Code:</label><br>
        <input type="text" id="course_code" name="course_code" required><br><br>
        <label for="description">Course Description:</label><br>
        <textarea id="description" name="description" rows="4" cols="50" required></textarea><br><br>
        <label for="teacher">Assign Teacher:</label><br>
        <select id="teacher" name="teacher">
            <?php
            
            ?>
        </select><br><br>
        <button type="submit">Add Course</button>
    </form>
    </div>
    <div class="assign_teacher">
        <h2>Assign Teacher to Course</h2>
        <form>
            <label for="select_course">Select Course:</label><br>
            <select id="select_course" name="select_course">
                <?php
                
                ?>
            </select><br><br>
            <label for="select_teacher">Select Teacher:</label><br>
            <select id="select_teacher" name="select_teacher">
                <?php
                
                ?>
            </select><br><br>
            <button type="submit">Assign Teacher</button>
    </div>
    </div>
</body>
</html>