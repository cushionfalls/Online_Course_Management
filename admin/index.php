<?php
session_start();
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
                <li><a href="manage_students.php">Manage Students</a></li>
                <li><a href="delete.php">Delete Data</a></li>
            </ul>
        </div>
    </aside>

    <main class="md:col-span-3">
        <div class="card bg-base-100 shadow-xl border border-base-200">
            <div class="card-body">
                <h1 class="card-title text-3xl mb-4">Welcome to the Admin Dashboard</h1>
                <p>Select an option from the menu to manage the system.</p>
                <div class="stats stats-vertical lg:stats-horizontal shadow mt-4">
                    <div class="stat">
                        <div class="stat-title">System Status</div>
                        <div class="stat-value text-success">Active</div>
                        <div class="stat-desc">All systems operational</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>