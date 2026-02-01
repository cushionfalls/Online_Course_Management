<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
include '../includes/header.php';

$message = "";

// Handle Updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_course'])) {
        $id = $_POST['course_id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $category = trim($_POST['category']);
        $level = trim($_POST['level']);
        $instructor_id = !empty($_POST['instructor_id']) ? $_POST['instructor_id'] : NULL;

        $stmt = $conn->prepare("UPDATE courses SET title=?, description=?, category=?, level=?, instructor_id=? WHERE course_id=?");
        $stmt->bind_param("ssssii", $title, $description, $category, $level, $instructor_id, $id);
        if ($stmt->execute()) {
            $message = "Course updated successfully.";
        } else {
            $message = "Error updating course: " . $conn->error;
        }
        $stmt->close();
    } elseif (isset($_POST['update_user'])) {
        $id = $_POST['user_id'];
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        // Only update password if provided
        $password = trim($_POST['password']);

        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET fullname=?, email=?, password=? WHERE id=?");
            $stmt->bind_param("sssi", $fullname, $email, $hashed, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET fullname=?, email=? WHERE id=?");
            $stmt->bind_param("ssi", $fullname, $email, $id);
        }

        if ($stmt->execute()) {
            $message = "User updated successfully.";
        } else {
            $message = "Error updating user: " . $conn->error;
        }
        $stmt->close();
    } elseif (isset($_POST['assign_course'])) {
        $user_id = $_POST['user_id'];
        $course_id = $_POST['course_id'];
        
        $stmt = $conn->prepare("UPDATE courses SET instructor_id=? WHERE course_id=?");
        $stmt->bind_param("ii", $user_id, $course_id);
        
        if ($stmt->execute()) {
            $message = "Course assigned successfully.";
        } else {
            $message = "Error assigning course: " . $conn->error;
        }
        $stmt->close();
    }
}

// Check if we are in edit mode
$edit_course = null;
$edit_user = null;

if (isset($_GET['edit_course'])) {
    $stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $_GET['edit_course']);
    $stmt->execute();
    $edit_course = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
if (isset($_GET['edit_user'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit_user']);
    $stmt->execute();
    $edit_user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Fetch Lists
$users = [];
$courses = [];

if (!$edit_course && !$edit_user) {
    // Teachers
    $t_res = $conn->query("SELECT id, fullname, email, role FROM users WHERE role = 'teacher'");
    while ($row = $t_res->fetch_assoc()) $users[] = $row;
    
    // Courses
    $c_res = $conn->query("SELECT course_id, title, instructor_id FROM courses");
    while ($row = $c_res->fetch_assoc()) $courses[] = $row;
}
?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <aside class="md:col-span-1">
        <div class="bg-base-200 p-4 rounded-lg h-full">
            <h3 class="font-bold text-lg mb-4 px-4">Admin Menu</h3>
             <ul class="menu bg-base-100 rounded-box w-full shadow-sm">
                <li><a href="add_course.php">Add Course & Assign Teacher</a></li>
                <li><a href="add_teacher.php">Add Teacher</a></li>
                <li><a href="edit.php" class="active">Manage Courses & Teachers</a></li>
                <li><a href="manage_students.php">View Students</a></li>
                <li><a href="delete.php">Delete Data</a></li>
            </ul>
        </div>
    </aside>

    <main class="md:col-span-3">
        <?php if ($message != ""): ?>
            <div class="alert <?php echo strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-error'; ?> text-sm py-2 mb-4">
                <span><?php echo htmlspecialchars($message); ?></span>
                <a href="edit.php" class="btn btn-xs">Back to List</a>
            </div>
        <?php endif; ?>

        <?php if ($edit_course): ?>
            <!-- Edit Course Form -->
            <div class="card bg-base-100 shadow-xl border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-2xl mb-6">Edit Course: <?php echo htmlspecialchars($edit_course['title']); ?></h2>
                    <form action="edit.php" method="post">
                        <input type="hidden" name="course_id" value="<?php echo $edit_course['course_id']; ?>">
                        
                        <div class="form-control w-full mb-4">
                            <label class="label"><span class="label-text">Course Title</span></label>
                            <input type="text" name="title" value="<?php echo htmlspecialchars($edit_course['title']); ?>" class="input input-bordered w-full" required />
                        </div>
                        <div class="form-control w-full mb-4">
                            <label class="label"><span class="label-text">Description</span></label>
                            <textarea name="description" class="textarea textarea-bordered h-24"><?php echo htmlspecialchars($edit_course['description']); ?></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text">Category</span></label>
                                <input type="text" name="category" value="<?php echo htmlspecialchars($edit_course['category']); ?>" class="input input-bordered w-full" />
                            </div>
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text">Level</span></label>
                                <select name="level" class="select select-bordered w-full">
                                    <option value="Beginner" <?php if($edit_course['level']=='Beginner') echo 'selected'; ?>>Beginner</option>
                                    <option value="Intermediate" <?php if($edit_course['level']=='Intermediate') echo 'selected'; ?>>Intermediate</option>
                                    <option value="Advanced" <?php if($edit_course['level']=='Advanced') echo 'selected'; ?>>Advanced</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-control w-full mb-6">
                            <label class="label"><span class="label-text">Instructor</span></label>
                            <select name="instructor_id" class="select select-bordered w-full">
                                <option value="">-- No Instructor --</option>
                                <?php 
                                $ts_res = $conn->query("SELECT id, fullname FROM users WHERE role='teacher'");
                                while($t = $ts_res->fetch_assoc()) {
                                    $sel = ($t['id'] == $edit_course['instructor_id']) ? 'selected' : '';
                                    echo "<option value='".$t['id']."' $sel>".htmlspecialchars($t['fullname'])."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="card-actions justify-end">
                            <a href="edit.php" class="btn btn-ghost">Cancel</a>
                            <button type="submit" name="update_course" class="btn btn-primary">Update Course</button>
                        </div>
                    </form>
                </div>
            </div>

        <?php elseif ($edit_user): ?>
            <!-- Edit User Form -->
            <div class="card bg-base-100 shadow-xl border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-2xl mb-6">Edit Teacher: <?php echo htmlspecialchars($edit_user['fullname']); ?></h2>
                    <form action="edit.php" method="post">
                        <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
                        
                        <div class="form-control w-full mb-4">
                            <label class="label"><span class="label-text">Full Name</span></label>
                            <input type="text" name="fullname" value="<?php echo htmlspecialchars($edit_user['fullname']); ?>" class="input input-bordered w-full" required />
                        </div>
                        <div class="form-control w-full mb-4">
                            <label class="label"><span class="label-text">Email</span></label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($edit_user['email']); ?>" class="input input-bordered w-full" required />
                        </div>
                        <div class="form-control w-full mb-6">
                            <label class="label"><span class="label-text">New Password (leave blank to keep current)</span></label>
                            <input type="password" name="password" class="input input-bordered w-full" />
                        </div>
                        <div class="card-actions justify-end">
                            <a href="edit.php" class="btn btn-ghost">Cancel</a>
                            <button type="submit" name="update_user" class="btn btn-primary">Update User</button>
                        </div>
                    </form>

                    <!-- Assign Course Section -->
                    <div class="divider"></div>
                    <h3 class="text-xl font-bold mb-4">Assign Course</h3>
                    <form action="edit.php" method="post" class="flex gap-4 items-end">
                        <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Select Course to Assign</span></label>
                            <select name="course_id" class="select select-bordered w-full">
                                <option value="">-- Select Course --</option>
                                <?php
                                $c_res = $conn->query("SELECT course_id, title, instructor_id FROM courses");
                                while($c = $c_res->fetch_assoc()) {
                                    $is_assigned = ($c['instructor_id'] == $edit_user['id']) ? ' (Assigned to this teacher)' : '';
                                    echo "<option value='".$c['course_id']."'>".htmlspecialchars($c['title']).$is_assigned."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" name="assign_course" class="btn btn-secondary">Assign</button>
                    </form>
                    
                    <div class="mt-4">
                        <h4 class="font-bold mb-2">Currently Assigned Courses:</h4>
                        <ul class="list-disc pl-5">
                            <?php
                            $my_courses = $conn->prepare("SELECT title FROM courses WHERE instructor_id = ?");
                            $my_courses->bind_param("i", $edit_user['id']);
                            $my_courses->execute();
                            $res = $my_courses->get_result();
                            if ($res->num_rows > 0) {
                                while($mc = $res->fetch_assoc()) {
                                    echo "<li>".htmlspecialchars($mc['title'])."</li>";
                                }
                            } else {
                                echo "<li class='text-gray-500'>No courses assigned.</li>";
                            }
                            $my_courses->close();
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Lists -->
            <div class="space-y-6">
                <div class="card bg-base-100 shadow-xl border border-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-4">Manage Teachers</h2>
                        
                        <div class="form-control w-full mb-4">
                            <input type="text" id="teacherSearch" placeholder="Search teachers by name or email..." class="input input-bordered w-full" />
                        </div>
                        
                        <div class="overflow-x-auto max-h-60">
                            <table class="table w-full">
                                <tbody id="teacherTableBody">
                                    <?php foreach ($users as $u): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($u['fullname']); ?></td>
                                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                                            <td class="text-right"><a href="edit.php?edit_user=<?php echo $u['id']; ?>" class="btn btn-sm btn-ghost">Edit</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-xl border border-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-4">Manage Courses</h2>
                        <div class="overflow-x-auto max-h-60">
                            <table class="table w-full">
                                <tbody>
                                    <?php foreach ($courses as $c): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($c['title']); ?></td>
                                            <td class="text-right"><a href="edit.php?edit_course=<?php echo $c['course_id']; ?>" class="btn btn-sm btn-ghost">Edit</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
const teacherSearch = document.getElementById('teacherSearch');
const teacherTableBody = document.getElementById('teacherTableBody');

if (teacherSearch && teacherTableBody) {
    let searchTimeout;
    
    loadTeachers('');
    
    teacherSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        
        searchTimeout = setTimeout(() => {
            const query = teacherSearch.value.trim();
            loadTeachers(query);
        }, 300);
    });
    
    function loadTeachers(query) {
        fetch(`search_api.php?action=search_teachers&q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error:', data.error);
                    return;
                }
                
                teacherTableBody.innerHTML = '';
                
                if (data.length === 0) {
                    teacherTableBody.innerHTML = '<tr><td colspan="3" class="text-center">No teachers found.</td></tr>';
                } else {
                    data.forEach(teacher => {
                        const row = `
                            <tr>
                                <td>${escapeHtml(teacher.fullname)}</td>
                                <td>${escapeHtml(teacher.email)}</td>
                                <td class="text-right"><a href="edit.php?edit_user=${teacher.id}" class="btn btn-sm btn-ghost">Edit</a></td>
                            </tr>
                        `;
                        teacherTableBody.innerHTML += row;
                    });
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}
</script>

<?php include '../includes/footer.php'; ?>