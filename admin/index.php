<?php
session_start();
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
include '../includes/header.php'; ?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <aside class="md:col-span-1">
        <div class="bg-base-200 p-4 rounded-lg h-full">
            <h3 class="font-bold text-lg mb-4 px-4">Admin Menu</h3>
            <ul class="menu bg-base-100 rounded-box w-full shadow-sm">
                <li><a href="add_course.php">Add Course & Assign Teacher</a></li>
                <li><a href="add_teacher.php">Add Teacher</a></li>
                <li><a href="edit.php">Manage Courses & Teachers</a></li>
                <li><a href="manage_students.php">View Students</a></li>
                <li><a href="delete.php">Delete Data</a></li>
            </ul>
        </div>
    </aside>

    <main class="md:col-span-3">
        <div class="card bg-base-100 shadow-xl border border-base-200">
            <div class="card-body">
                <h1 class="card-title text-3xl mb-4">Welcome to the Admin Dashboard</h1>
                <p>Select an option from the menu to manage the system.</p>
                
                <?php
                // Fetch counts
                $course_count = 0;
                $teacher_count = 0;
                $student_count = 0;

                // Courses
                $stmt = $conn->query("SELECT COUNT(*) as count FROM courses");
                if ($stmt) {
                    $row = $stmt->fetch_assoc();
                    $course_count = $row['count'];
                }

                // Teachers
                $stmt = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'teacher'");
                if ($stmt) {
                    $row = $stmt->fetch_assoc();
                    $teacher_count = $row['count'];
                }

                // Students
                $stmt = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'");
                if ($stmt) {
                    $row = $stmt->fetch_assoc();
                    $student_count = $row['count'];
                }
                ?>

                <div class="stats stats-vertical lg:stats-horizontal shadow mt-8 w-full">
                    <div class="stat place-items-center">
                        <div class="stat-title">Total Courses</div>
                        <div class="stat-value text-primary"><?php echo $course_count; ?></div>
                        <div class="stat-desc">Available across all categories</div>
                    </div>
                    
                    <div class="stat place-items-center">
                        <div class="stat-title">Total Teachers</div>
                        <div class="stat-value text-secondary"><?php echo $teacher_count; ?></div>
                        <div class="stat-desc">Active instructors</div>
                    </div>
                    
                    <div class="stat place-items-center">
                        <div class="stat-title">Total Students</div>
                        <div class="stat-value text-accent"><?php echo $student_count; ?></div>
                        <div class="stat-desc">Enrolled learners</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>