<?php
session_start();
require_once 'Database/db_connect.php';
require_once 'functions.php';
check_auth('admin');

$project_id = (int)($_GET['project_id'] ?? 0);
$task_to_edit = null;
$errors = [];
$message = '';
$mode = 'Create'; 

if ($project_id === 0) {
    header('Location: admin_dashboard.php?error=noproject');
    exit;
}

$stmt_project = $pdo->prepare("SELECT project_name, assigned_user_id FROM projects WHERE id = ?");
$stmt_project->execute([$project_id]);
$project = $stmt_project->fetch();

if (!$project) {
    header('Location: admin_dashboard.php?error=invalidproject');
    exit;
}

$project_name = htmlspecialchars($project['project_name']);

$stmt_users = $pdo->query("SELECT id, name FROM users WHERE role = 'user' ORDER BY name ASC");
$users = $stmt_users->fetchAll();
$priorities = ['Low', 'Medium', 'High'];
$statuses = ['Not Started', 'In Progress', 'Completed'];

if (isset($_GET['edit_id'])) {
    $task_id = (int)$_GET['edit_id'];
    $stmt_task = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND project_id = ?");
    $stmt_task->execute([$task_id, $project_id]);
    $task_to_edit = $stmt_task->fetch();
    
    if ($task_to_edit) {
        $mode = 'Edit';
    } else {
        $message = "<div class='alert alert-warning'>Task not found. Reverting to Create mode.</div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_submit'])) {
    $task_id = (int)($_POST['task_id'] ?? 0);
    $title = trim($_POST['task_title'] ?? '');
    $description = trim($_POST['task_description'] ?? '');
    $assigned_user_id = (int)($_POST['assigned_user_id'] ?? 0);
    $priority = trim($_POST['priority'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    $status = trim($_POST['status'] ?? 'Not Started'); 

    if (empty($title) || empty($deadline) || $assigned_user_id === 0 || !in_array($priority, $priorities)) {
        $errors[] = "Please fill all required fields and select valid options.";
    }

    if (empty($errors)) {
        try {
            if ($task_id > 0) {
                $sql = "UPDATE tasks SET task_title = ?, description = ?, assigned_user_id = ?, priority = ?, deadline = ?, status = ? WHERE id = ? AND project_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$title, $description, $assigned_user_id, $priority, $deadline, $status, $task_id, $project_id]);
                $message = "<div class='alert alert-success'>Task updated successfully!</div>";
            } else {
                $sql = "INSERT INTO tasks (project_id, task_title, description, assigned_user_id, priority, deadline, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$project_id, $title, $description, $assigned_user_id, $priority, $deadline, 'Not Started']);
                $message = "<div class='alert alert-success'>Task created successfully!</div>";
            }
            header("Location: tasks.php?project_id={$project_id}&msg=success");
            exit;

        } catch (Exception $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    $task_to_edit = [
        'id' => $task_id, 'task_title' => $title, 'description' => $description, 
        'assigned_user_id' => $assigned_user_id, 'priority' => $priority, 
        'deadline' => $deadline, 'status' => $status, 'project_id' => $project_id
    ];
}
if (isset($_GET['delete_id'])) {
    $task_id = (int)$_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND project_id = ?");
        $stmt->execute([$task_id, $project_id]);
        header("Location: tasks.php?project_id={$project_id}&msg=deleted");
        exit;
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Error deleting task.</div>";
    }
}

$stmt_tasks = $pdo->prepare("SELECT t.*, u.name AS assigned_user_name 
                            FROM tasks t
                            JOIN users u ON t.assigned_user_id = u.id
                            WHERE t.project_id = ?
                            ORDER BY t.deadline ASC, FIELD(t.priority, 'High', 'Medium', 'Low') DESC");
$stmt_tasks->execute([$project_id]);
$tasks = $stmt_tasks->fetchAll();

$tasks_grouped = [
    'Not Started' => [],
    'In Progress' => [],
    'Completed' => []
];
foreach ($tasks as $task) {
    $tasks_grouped[$task['status']][] = $task;
}

$form_data = $task_to_edit ?: [
    'task_title' => '', 'description' => '', 'assigned_user_id' => $project['assigned_user_id'], 
    'priority' => 'Medium', 'deadline' => date('Y-m-d', strtotime('+1 day')), 'status' => 'Not Started', 'id' => 0
];
?>

<?php include 'admin_header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tasks for Project: <span class="text-primary"><?= $project_name ?></span></h2>
        <a href="admin_dashboard.php" class="btn btn-secondary">← Back to Projects</a>
    </div>

    <?= $message ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success') echo "<div class='alert alert-success'>Task saved successfully!</div>"; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') echo "<div class='alert alert-success'>Task deleted successfully!</div>"; ?>

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><?= $mode ?> Task</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul><?php foreach ($errors as $error) echo "<li>" . htmlspecialchars($error) . "</li>"; ?></ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="tasks.php?project_id=<?= $project_id ?>">
                <input type="hidden" name="task_id" value="<?= $form_data['id'] ?>">
                <input type="hidden" name="task_submit" value="1">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="task_title" class="form-label">Task Title</label>
                        <input type="text" class="form-control" id="task_title" name="task_title" value="<?= htmlspecialchars($form_data['task_title']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="assigned_user_id" class="form-label">Assigned User</label>
                        <select class="form-select" id="assigned_user_id" name="assigned_user_id" required>
                            <option value="">-- Select User --</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" <?= $user['id'] == $form_data['assigned_user_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="task_description" class="form-label">Description</label>
                    <textarea class="form-control" id="task_description" name="task_description" rows="2"><?= htmlspecialchars($form_data['description']) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select" id="priority" name="priority" required>
                            <?php foreach ($priorities as $p): ?>
                                <option value="<?= $p ?>" <?= $p == $form_data['priority'] ? 'selected' : '' ?>><?= $p ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="deadline" class="form-label">Deadline</label>
                        <input type="date" class="form-control" id="deadline" name="deadline" value="<?= htmlspecialchars($form_data['deadline']) ?>" required>
                    </div>
                    <?php if ($mode == 'Edit'): ?>
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <?php foreach ($statuses as $s): ?>
                                <option value="<?= $s ?>" <?= $s == $form_data['status'] ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                    <a href="tasks.php?project_id=<?= $project_id ?>" class="btn btn-outline-secondary me-md-2">Cancel / New Task</a>
                    <button type="submit" class="btn btn-primary"><?= $mode ?> Task</button>
                </div>
            </form>
        </div>
    </div>
    
    <h3 class="mb-3 mt-5">Task List</h3>
    
    <div class="row">
        <?php 
        $col_classes = [
            'Not Started' => 'bg-secondary', 
            'In Progress' => 'bg-primary', 
            'Completed' => 'bg-success'
        ];
        
        foreach ($tasks_grouped as $status => $task_list): 
        ?>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header text-white <?= $col_classes[$status] ?>">
                    <h5 class="mb-0"><?= $status ?> (<?= count($task_list) ?>)</h5>
                </div>
                <div class="list-group list-group-flush" style="min-height: 150px;">
                    <?php if (empty($task_list)): ?>
                        <div class="p-3 text-center text-muted">No tasks.</div>
                    <?php else: ?>
                        <?php foreach ($task_list as $task): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= htmlspecialchars($task['task_title']) ?></h6>
                                    <small class="badge bg-info text-dark"><?= $task['priority'] ?></small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center small text-muted">
                                    <span>Assignee: <?= htmlspecialchars($task['assigned_user_name']) ?></span>
                                    <span>Due: <?= date('M j', strtotime($task['deadline'])) ?></span>
                                </div>
                                <div class="mt-2 d-flex justify-content-end">
                                    <a href="tasks.php?project_id=<?= $project_id ?>&edit_id=<?= $task['id'] ?>" class="btn btn-sm btn-outline-info me-2">Edit</a>
                                    <a href="tasks.php?project_id=<?= $project_id ?>&delete_id=<?= $task['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Confirm deletion of this task?');">Delete</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<?php include 'footer.php'; ?>