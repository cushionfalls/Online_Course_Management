<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
include '../includes/header.php';

$message = "";

// Handle Deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_user'])) {
        $id = $_POST['user_id'];
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "User deleted successfully.";
        } else {
            $message = "Error deleting user: " . $conn->error;
        }
        $stmt->close();
    } elseif (isset($_POST['delete_course'])) {
        $id = $_POST['course_id'];
        $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Course deleted successfully.";
        } else {
            $message = "Error deleting course: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch Users (Teachers and Students only, avoid deleting self/admin if possible though Logic implies generalized delete)
// For safety, let's just show all users except the current logged in one effectively? Or just list all.
// The file says "Delete User Data".
$users = [];
$u_res = $conn->query("SELECT id, username, role, fullname FROM users");
if ($u_res) {
    while ($row = $u_res->fetch_assoc()) {
        $users[] = $row;
    }
}

// Fetch Courses
$courses = [];
$c_res = $conn->query("SELECT course_id, title FROM courses");
if ($c_res) {
    while ($row = $c_res->fetch_assoc()) {
        $courses[] = $row;
    }
}
?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <aside class="md:col-span-1">
        <div class="bg-base-200 p-4 rounded-lg h-full">
            <h3 class="font-bold text-lg mb-4 px-4">Admin Menu</h3>
             <ul class="menu bg-base-100 rounded-box w-full shadow-sm">
                <li><a href="add_course.php">Add Course & Assign Teacher</a></li>
                <li><a href="add_teacher.php">Add Teacher</a></li>
                <li><a href="edit.php">Manage Courses & Teachers</a></li>
                <li><a href="manage_students.php">Manage Students</a></li>
                <li><a href="delete.php" class="active">Delete Data</a></li>
            </ul>
        </div>
    </aside>

    <main class="md:col-span-3 space-y-6">
        <?php if ($message != ""): ?>
            <div class="alert <?php echo strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-error'; ?> text-sm py-2">
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
        <?php endif; ?>

        <!-- Delete Users Section -->
        <div class="card bg-base-100 shadow-xl border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-4">Delete Users</h2>
                <div class="overflow-x-auto max-h-60">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['fullname']); ?> (<?php echo htmlspecialchars($user['username']); ?>)</td>
                                    <td><div class="badge badge-outline"><?php echo htmlspecialchars($user['role']); ?></div></td>
                                    <td>
                                        <?php if($user['id'] != $_SESSION['user_id']): ?>
                                        <form action="" method="post" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="delete_user" class="btn btn-error btn-xs text-white">Delete</button>
                                        </form>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">Current User</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Delete Courses Section -->
        <div class="card bg-base-100 shadow-xl border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-4">Delete Courses</h2>
                <div class="overflow-x-auto max-h-60">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Course Title</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($course['title']); ?></td>
                                    <td>
                                        <form action="" method="post" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                            <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                                            <button type="submit" name="delete_course" class="btn btn-error btn-xs text-white">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>