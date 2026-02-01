<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}
require_once __DIR__ . '/../config/db.php';
include '../includes/header.php';

$msg = "";
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'enrolled_success') $msg = "Successfully enrolled in the course!";
    elseif ($_GET['msg'] == 'already_enrolled') $msg = "You are already enrolled in this course.";
    elseif ($_GET['msg'] == 'enrolled_error') $msg = "Error enrolling in course.";
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$courses = [];

$sql = "SELECT c.*, u.fullname as instructor_name FROM courses c 
        LEFT JOIN users u ON c.instructor_id = u.id";

if ($search) {
    $sql .= " WHERE c.title LIKE ? OR c.description LIKE ? OR c.category LIKE ?";
    $stmt = $conn->prepare($sql);
    $like = "%$search%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql .= " ORDER BY c.created_at DESC";
    $result = $conn->query($sql);
}

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

// Fetch Enrolled Courses & Assignments
$my_enrollments = [];
if (isset($_SESSION['user_id'])) {
    $e_sql = "SELECT c.*, u.fullname as instructor_name 
              FROM enrollments e 
              JOIN courses c ON e.course_id = c.course_id 
              LEFT JOIN users u ON c.instructor_id = u.id
              WHERE e.student_id = ?";
    $stmt = $conn->prepare($e_sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $e_result = $stmt->get_result();
    while ($row = $e_result->fetch_assoc()) {
        // Fetch assignments for this course
        $a_sql = "SELECT * FROM assignments WHERE course_id = ? ORDER BY due_date ASC";
        $a_stmt = $conn->prepare($a_sql);
        $a_stmt->bind_param("i", $row['course_id']);
        $a_stmt->execute();
        $a_res = $a_stmt->get_result();
        $assignments = [];
        while ($a = $a_res->fetch_assoc()) {
            $assignments[] = $a;
        }
        $row['assignments'] = $assignments;
        $my_enrollments[] = $row;
        $a_stmt->close();
    }
    $stmt->close();
}
?>

<div class="max-w-6xl mx-auto space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold">Available Courses</h1>
            <p class="text-gray-500 mt-1">Browse and search for courses to enhance your skills.</p>
             <?php if ($msg): ?>
                <div class="alert alert-info py-2 mt-2">
                    <span><?php echo htmlspecialchars($msg); ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Search Form -->
        <form action="" method="get" class="w-full md:w-auto">
            <div class="join w-full">
                <input type="text" name="search" placeholder="Search courses..." value="<?php echo htmlspecialchars($search); ?>" class="input input-bordered join-item w-full md:w-80" />
                <button type="submit" class="btn btn-primary join-item">Search</button>
            </div>
        </form>
    </div>

    <!-- My Enrollments Section -->
    <?php if (!empty($my_enrollments)): ?>
        <div class="divider"></div>
        <h2 class="text-2xl font-bold mb-4">My Enrollments</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php foreach($my_enrollments as $course): ?>
                <div class="card bg-base-100 shadow-xl border border-primary">
                    <div class="card-body">
                        <div class="flex justify-between items-start">
                            <h2 class="card-title text-xl font-bold">
                                <?php echo htmlspecialchars($course['title']); ?>
                            </h2>
                            <?php if(!empty($course['level'])): ?>
                                <div class="badge badge-accent badge-outline text-xs"><?php echo htmlspecialchars($course['level']); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <p class="text-sm text-gray-500 mb-2">
                            Instructor: <span class="font-medium text-base-content"><?php echo htmlspecialchars($course['instructor_name'] ?? 'TBA'); ?></span>
                        </p>
                        
                        <div class="collapse collapse-arrow bg-base-200 mt-2">
                            <input type="checkbox" /> 
                            <div class="collapse-title font-medium text-sm">
                                View Assignments (<?php echo count($course['assignments']); ?>)
                            </div>
                            <div class="collapse-content"> 
                                <ul class="list-disc pl-5 text-sm">
                                    <?php if(empty($course['assignments'])): ?>
                                        <li>No assignments yet.</li>
                                    <?php else: ?>
                                        <?php foreach($course['assignments'] as $assign): ?>
                                            <li class="mb-1">
                                                <span class="font-bold"><?php echo htmlspecialchars($assign['title']); ?></span>
                                                <?php if($assign['due_date']): ?>
                                                    <br><span class="text-xs text-gray-500">Due: <?php echo date('M d, Y', strtotime($assign['due_date'])); ?></span>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>

                        <div class="card-actions justify-end mt-4">
                            <button class="btn btn-primary btn-sm btn-outline" disabled>Enrolled</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Course Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($courses as $course): ?>
            <div class="card bg-base-100 shadow-xl border border-base-200 hover:border-primary transition-colors duration-300">
                <div class="card-body">
                    <div class="flex justify-between items-start">
                        <h2 class="card-title text-xl font-bold">
                            <?php echo htmlspecialchars($course['title']); ?>
                        </h2>
                        <?php if(!empty($course['level'])): ?>
                            <div class="badge badge-accent badge-outline text-xs"><?php echo htmlspecialchars($course['level']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <p class="text-sm text-gray-500 mb-2">
                        Instructor: <span class="font-medium text-base-content"><?php echo htmlspecialchars($course['instructor_name'] ?? 'TBA'); ?></span>
                    </p>
                    
                    <p class="text-gray-600 line-clamp-3 mb-4">
                        <?php echo htmlspecialchars($course['description']); ?>
                    </p>
                    
                    <div class="card-actions justify-between items-center mt-auto">
                        <div class="badge badge-ghost"><?php echo htmlspecialchars($course['category'] ?? 'General'); ?></div>
                        <a href="enroll.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-primary btn-sm">Enroll Now</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if(empty($courses)): ?>
        <div class="alert alert-info shadow-sm mt-8">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>No courses found matching your search.</span>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
