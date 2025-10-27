<?php
session_start();
require_once 'Database/db_connect.php';
require_once 'functions.php';
check_auth('admin'); 

$project = null;
$users = [];
$errors = [];
$mode = 'Create'; 

try {
    $stmt_users = $pdo->query("SELECT id, name FROM users WHERE role = 'user' ORDER BY name ASC");
    $users = $stmt_users->fetchAll();
} catch (Exception $e) {
    die("Could not load users for assignment: " . $e->getMessage());
}

if (isset($_GET['id'])) {
    $mode = 'Edit';
    $project_id = (int)$_GET['id'];
    
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch();

    if (!$project) {
        header('Location: admin_dashboard.php?msg=notfound');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['project_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $assigned_user_id = (int)($_POST['assigned_user_id'] ?? 0);
    
    if (empty($name) || empty($start_date) || empty($end_date) || $assigned_user_id == 0) {
        $errors[] = "All required fields must be filled.";
    }
    if (strtotime($start_date) > strtotime($end_date)) {
        $errors[] = "The end date (deadline) must be on or after the start date.";
    }
    if (!in_array($assigned_user_id, array_column($users, 'id'))) {
        $errors[] = "Invalid user assignment.";
    }

    if (empty($errors)) {
        try {
            if ($mode == 'Create') {
                $sql = "INSERT INTO projects (project_name, description, start_date, end_date, assigned_user_id) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $description, $start_date, $end_date, $assigned_user_id]);
                header('Location: admin_dashboard.php?msg=created');
                exit;

            } elseif ($mode == 'Edit') {
                $sql = "UPDATE projects SET project_name = ?, description = ?, start_date = ?, end_date = ?, assigned_user_id = ? 
                        WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $description, $start_date, $end_date, $assigned_user_id, $project['id']]);
                header('Location: admin_dashboard.php?msg=updated');
                exit;
            }
        } catch (Exception $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
  
    $project = [
        'project_name' => $name, 'description' => $description, 'start_date' => $start_date, 
        'end_date' => $end_date, 'assigned_user_id' => $assigned_user_id, 'id' => $project['id'] ?? null
    ];
} 

$display_name = $project['project_name'] ?? '';
$display_description = $project['description'] ?? '';
$display_start_date = $project['start_date'] ?? date('Y-m-d');
$display_end_date = $project['end_date'] ?? date('Y-m-d', strtotime('+7 days'));
$display_assigned_user_id = $project['assigned_user_id'] ?? 0;

include 'admin_header.php'; 
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?= $mode ?> Project</h2>
        <a href="admin_dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Validation Errors:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST">
                
                <div class="mb-3">
                    <label for="project_name" class="form-label">Project Name</label>
                    <input type="text" class="form-control" id="project_name" name="project_name" 
                           value="<?= htmlspecialchars($display_name) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($display_description) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="<?= htmlspecialchars($display_start_date) ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">End Date (Deadline)</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="<?= htmlspecialchars($display_end_date) ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="assigned_user_id" class="form-label">Assigned User</label>
                    <select class="form-select" id="assigned_user_id" name="assigned_user_id" required>
                        <option value="">-- Select User --</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" 
                                <?= $user['id'] == $display_assigned_user_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg"><?= $mode ?> Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
include 'footer.php'; 
?>