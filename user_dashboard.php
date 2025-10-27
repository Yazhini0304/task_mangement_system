<?php
session_start();
require_once 'Database/db_connect.php';
require_once 'functions.php';
check_auth('user');

$user_id = $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['user_name']);

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $task_id = $_POST['task_id'] ?? 0;
    $new_status = $_POST['status'] ?? 'In Progress'; 

    if ($task_id > 0 && in_array($new_status, ['In Progress', 'Completed'])) {
        try {
            $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ? AND assigned_user_id = ?");
            $stmt->execute([$new_status, $task_id, $user_id]);

            header('Location: user_dashboard.php?msg=status_updated');
            exit;
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>Error updating task status.</div>";
        }
    }
}
if (isset($_GET['msg']) && $_GET['msg'] === 'status_updated') {
    $message = "<div class='alert alert-success'>Task status updated successfully!</div>";
}

$stmt_projects = $pdo->prepare("SELECT id, project_name, description, end_date FROM projects WHERE assigned_user_id = ? ORDER BY end_date ASC");
$stmt_projects->execute([$user_id]);
$assigned_projects = $stmt_projects->fetchAll();

$stmt_tasks = $pdo->prepare("SELECT id, project_id, task_title, description, priority, deadline, status 
                            FROM tasks 
                            WHERE assigned_user_id = ? 
                            ORDER BY project_id, deadline ASC");
$stmt_tasks->execute([$user_id]);
$assigned_tasks_raw = $stmt_tasks->fetchAll();
$tasks_by_project = [];
foreach ($assigned_tasks_raw as $task) {
    $tasks_by_project[$task['project_id']][] = $task;
}
include 'user_header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .project-card-header { cursor: pointer; }       
    </style>
</head>
<body>
    

    <div class="container mt-5">
        <h2 class="mb-4">My Dashboard</h2>
        
        <?= $message ?>

        <?php if (empty($assigned_projects) && empty($assigned_tasks_raw)): ?>
            <div class="alert alert-info" role="alert">
                You currently have no projects or tasks assigned.
            </div>
        <?php else: ?>
            <div class="accordion" id="projectAccordion">
                <?php foreach ($assigned_projects as $project): 
                    $project_id = $project['id'];
                    $tasks = $tasks_by_project[$project_id] ?? [];
                ?>
                <div class="accordion-item mb-3 shadow-sm">
                    <h2 class="accordion-header" id="heading<?= $project_id ?>">
                        <button class="accordion-button project-card-header <?= $tasks ? 'collapsed' : 'text-muted' ?>" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#collapse<?= $project_id ?>" aria-expanded="true" aria-controls="collapse<?= $project_id ?>">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h5><?= htmlspecialchars($project['project_name']) ?></h5>
                                <div class="ms-4">
                                    <small class="text-muted me-3">Deadline: <?= date('M j, Y', strtotime($project['end_date'])) ?></small>
                                    <?= get_deadline_indicator($project['end_date']) ?>
                                </div>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse<?= $project_id ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $project_id ?>" data-bs-parent="#projectAccordion">
                        <div class="accordion-body">
                            <p class="text-muted small">Project Description: <?= nl2br(htmlspecialchars($project['description'])) ?></p>
                            
                            <hr>
                            
                            <?php if (empty($tasks)): ?>
                                <p class="text-center text-secondary">No specific tasks assigned under this project yet.</p>
                            <?php else: ?>
                                <h6 class="mb-3">Assigned Tasks:</h6>
                                
                                <?php foreach ($tasks as $task): ?>
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0 text-primary"><?= htmlspecialchars($task['task_title']) ?></h6>
                                                <div>
                                                    <?= get_status_badge($task['status']) ?>
                                                </div>
                                            </div>
                                            <p class="card-text small"><?= nl2br(htmlspecialchars($task['description'])) ?></p>
                                            
                                            <div class="d-flex justify-content-between align-items-center small text-muted">
                                                <span>Priority: <span class="badge bg-secondary"><?= $task['priority'] ?></span></span>
                                                <span>Due: <?= date('M j, Y', strtotime($task['deadline'])) ?></span>
                                            </div>
                                            
                                            <hr class="my-2">
                                            
                                            <form method="POST" class="d-flex justify-content-end">
                                                <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                                <input type="hidden" name="update_status" value="1">
                                                
                                                <?php if ($task['status'] !== 'Completed'): ?>
                                                    <button type="submit" name="status" value="Completed" class="btn btn-sm btn-success me-2">Mark Completed</button>
                                                <?php endif; ?>
                                                
                                                <?php if ($task['status'] !== 'In Progress' && $task['status'] !== 'Completed'): ?>
                                                    <button type="submit" name="status" value="In Progress" class="btn btn-sm btn-primary">Start Task</button>
                                                <?php endif; ?>
                                                
                                                <?php if ($task['status'] === 'Completed'): ?>
                                                    <button type="submit" name="status" value="In Progress" class="btn btn-sm btn-warning">Revert to In Progress</button>
                                                <?php endif; ?>
                                            </form>
                                            
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
include 'footer.php';
?>