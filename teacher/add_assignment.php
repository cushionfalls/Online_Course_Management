<?php
require_once __DIR__ . '/../config/db.php';
include '../includes/header.php';

$message = "";


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../auth/login.php");
    exit;
}

$courses = [];
$c_stmt = $conn->prepare("SELECT course_id, title FROM courses WHERE instructor_id = ?");
$c_stmt->bind_param("i", $_SESSION['user_id']);
$c_stmt->execute();
$c_result = $c_stmt->get_result();
while ($row = $c_result->fetch_assoc()) {
    $courses[] = $row;
}
$c_stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = $_POST['course_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];

    if (empty($course_id) || empty($title) || empty($due_date)) {
        $message = "All fields except description are required.";
    } else {

        $valid_course = false;
        foreach ($courses as $c) {
            if ($c['course_id'] == $course_id) {
                $valid_course = true;
                break;
            }
        }

        if ($valid_course) {
            $stmt = $conn->prepare("INSERT INTO assignments (course_id, title, description, due_date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $course_id, $title, $description, $due_date);
            if ($stmt->execute()) {
                $message = "Assignment added successfully!";
            } else {
                $message = "Error adding assignment: " . $conn->error;
            }
            $stmt->close();
        } else {
            $message = "Invalid Course Selection.";
        }
    }
}
?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <aside class="md:col-span-1">
        <div class="bg-base-200 p-4 rounded-lg h-full">
            <h3 class="font-bold text-lg mb-4 px-4">Teacher Menu</h3>
             <ul class="menu bg-base-100 rounded-box w-full shadow-sm">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="add_assignment.php" class="active">Add Assignment</a></li>
            </ul>
        </div>
    </aside>

    <main class="md:col-span-3">
        <div class="card bg-base-100 shadow-xl border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6">Add New Assignment</h2>
                
                <?php if ($message != ""): ?>
                    <div class="alert <?php echo strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-error'; ?> text-sm py-2 mb-4">
                        <span><?php echo htmlspecialchars($message); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (empty($courses)): ?>
                    <div class="alert alert-warning">
                        <span>You are not assigned to any courses yet. Please contact an administrator.</span>
                    </div>
                <?php else: ?>
                    <form action="" method="post">
                        <div class="form-control w-full mb-4">
                            <label class="label"><span class="label-text">Select Course</span></label>
                            <select name="course_id" class="select select-bordered w-full" required>
                                <option value="">-- Select Course --</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['course_id']; ?>"><?php echo htmlspecialchars($course['title']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-control w-full mb-4">
                            <label class="label"><span class="label-text">Assignment Title</span></label>
                            <input type="text" name="title" class="input input-bordered w-full" required />
                        </div>
                        
                        <div class="form-control w-full mb-4">
                            <label class="label"><span class="label-text">Description</span></label>
                            <textarea name="description" class="textarea textarea-bordered h-24"></textarea>
                        </div>

                        <div class="form-control w-full mb-6">
                            <label class="label"><span class="label-text">Due Date</span></label>
                            <input type="datetime-local" name="due_date" class="input input-bordered w-full" required />
                        </div>

                        <div class="card-actions justify-end">
                            <button type="submit" class="btn btn-primary">Add Assignment</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
