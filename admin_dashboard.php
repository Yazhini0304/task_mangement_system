<?php
session_start();
require_once 'Database/db_connect.php';
require_once 'functions.php';
check_auth('admin'); 

// Fetch all projects with the assigned user's name
$stmt = $pdo->query("SELECT p.id, p.project_name, p.end_date, u.name AS assigned_user
                     FROM projects p
                     JOIN users u ON p.assigned_user_id = u.id
                     ORDER BY p.end_date ASC");
$projects = $stmt->fetchAll();

// Handle Delete Project
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $project_id = (int)$_GET['id'];
    try {
        
        $pdo->prepare("DELETE FROM tasks WHERE project_id = ?")->execute([$project_id]);
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        
       
        header('Location: admin_dashboard.php?msg=deleted');
        exit;
    } catch (Exception $e) {
        $delete_error = "Error deleting project: " . $e->getMessage();
    }
}


include 'admin_header.php'; 
?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Admin Dashboard - Project List</h2>
        <a href="project_form.php" class="btn btn-primary">➕ Create New Project</a>
    </div>

    <?php 

    if (isset($_GET['msg'])) {
        $msg_type = 'success';
        $msg_text = '';
        switch ($_GET['msg']) {
            case 'deleted': $msg_text = 'Project deleted successfully!'; break;
            case 'updated': $msg_text = 'Project updated successfully!'; break;
            case 'created': $msg_text = 'Project created successfully!'; break;
            default: $msg_type = '';
        }
        if ($msg_type) {
            echo "<div class='alert alert-$msg_type'>$msg_text</div>";
        }
    }
    if (isset($delete_error)): ?>
        <div class="alert alert-danger"><?= $delete_error ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Assigned User</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($projects)): ?>
                        <tr><td colspan="5" class="text-center text-muted">No projects found.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?= htmlspecialchars($project['project_name']) ?></td>
                            <td><?= htmlspecialchars($project['assigned_user']) ?></td>
                            <td><?= date('M j, Y', strtotime($project['end_date'])) ?></td>
                            <td><?= get_deadline_indicator($project['end_date']) ?></td>
                            <td>
                                <a href="tasks.php?project_id=<?= $project['id'] ?>" class="btn btn-sm btn-dark me-2">
                                    Manage Tasks
                                </a>
                                
                                <a href="project_form.php?id=<?= $project['id'] ?>" class="btn btn-sm btn-info me-2">Edit</a>
                                <a href="admin_dashboard.php?action=delete&id=<?= $project['id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this project and all its tasks?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php 
include 'footer.php'; 
?>