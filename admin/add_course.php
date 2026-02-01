<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
include '../includes/header.php';

$message = "";

// Fetch teachers for dropdown
$teachers = [];
$t_result = $conn->query("SELECT id, fullname FROM users WHERE role = 'teacher'");
if ($t_result) {
    while ($row = $t_result->fetch_assoc()) {
        $teachers[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $level = trim($_POST['level']);
    $instructor_id = !empty($_POST['instructor_id']) ? $_POST['instructor_id'] : NULL;

    if (empty($title)) {
        $message = "Course Title is required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO courses (title, description, category, level, instructor_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $description, $category, $level, $instructor_id);
        
        if ($stmt->execute()) {
            $message = "Course added successfully!";
        } else {
            $message = "Error adding course: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <aside class="md:col-span-1">
        <div class="bg-base-200 p-4 rounded-lg h-full">
            <h3 class="font-bold text-lg mb-4 px-4">Admin Menu</h3>
             <ul class="menu bg-base-100 rounded-box w-full shadow-sm">
                <li><a href="add_course.php" class="active">Add Course & Assign Teacher</a></li>
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
                <h2 class="card-title text-2xl mb-6">Add Course & Assign Teacher</h2>
                
                <?php if ($message != ""): ?>
                    <div class="alert <?php echo strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-error'; ?> text-sm py-2 mb-4">
                        <span><?php echo htmlspecialchars($message); ?></span>
                    </div>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="form-control w-full mb-4">
                        <label class="label"><span class="label-text">Course Title</span></label>
                        <input type="text" name="title" class="input input-bordered w-full" required />
                    </div>
                    
                    <div class="form-control w-full mb-4">
                        <label class="label"><span class="label-text">Course Description</span></label>
                        <textarea name="description" class="textarea textarea-bordered h-24"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Category</span></label>
                            <input type="text" name="category" class="input input-bordered w-full" placeholder="e.g. Programming" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Level</span></label>
                            <select name="level" class="select select-bordered w-full">
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-control w-full mb-6">
                        <label class="label"><span class="label-text">Assign Instructor</span></label>
                        <select name="instructor_id" class="select select-bordered w-full">
                            <option value="">-- Select Teacher --</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?php echo $teacher['id']; ?>"><?php echo htmlspecialchars($teacher['fullname']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="card-actions justify-end">
                        <button type="submit" class="btn btn-primary">Save Course</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>