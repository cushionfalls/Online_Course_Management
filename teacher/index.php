<?php
require_once __DIR__ . '/../config/db.php';
include '../includes/header.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../auth/login.php");
    exit;
}


$courses = [];
$c_stmt = $conn->prepare("SELECT course_id, title, description, level, category FROM courses WHERE instructor_id = ?");
$c_stmt->bind_param("i", $_SESSION['user_id']);
$c_stmt->execute();
$c_result = $c_stmt->get_result();
while ($row = $c_result->fetch_assoc()) {
    $courses[] = $row;
}
$c_stmt->close();

$students = [];
$s_result = $conn->query("SELECT id, fullname, email, username FROM users WHERE role = 'student'");
if ($s_result) {
    while ($row = $s_result->fetch_assoc()) {
        $students[] = $row;
    }
}
?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <aside class="md:col-span-1">
        <div class="bg-base-200 p-4 rounded-lg h-full">
            <h3 class="font-bold text-lg mb-4 px-4">Teacher Menu</h3>
            <ul class="menu bg-base-100 rounded-box w-full shadow-sm">
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="add_assignment.php">Add Assignment</a></li>
            </ul>
        </div>
    </aside>

    <main class="md:col-span-3 space-y-8">

        <div class="card bg-base-100 shadow-xl border border-base-200">
            <div class="card-body">
                <h1 class="card-title text-3xl mb-2">Teacher Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
            </div>
        </div>


        <div>
            <h2 class="text-2xl font-bold mb-4">My Courses</h2>
            <?php if (empty($courses)): ?>
                <div class="alert alert-info">You have not been assigned any courses yet.</div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($courses as $course): ?>
                        <div class="card bg-base-100 shadow-md border border-base-200">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                                <div class="badge badge-outline"><?php echo htmlspecialchars($course['category']); ?></div>
                                <div class="badge badge-secondary badge-outline"><?php echo htmlspecialchars($course['level']); ?></div>
                                <p class="text-sm text-gray-500 mt-2 line-clamp-2"><?php echo htmlspecialchars($course['description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>


        <div class="card bg-base-100 shadow-xl border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-4">Enrolled Students</h2>
                <div class="overflow-x-auto max-h-96">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Username</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($students)): ?>
                                <tr><td colspan="3" class="text-center">No students found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['fullname']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo htmlspecialchars($student['username']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
</div>

<?php include '../includes/footer.php'; ?>
