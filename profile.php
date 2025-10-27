<?php
session_start();
require_once 'Database/db_connect.php';
check_auth('user');

$user_id = $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['user_name']);
$message = '';

//Handle Profile Update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    
    $update_fields = ['name = ?', 'email = ?'];
    $update_values = [$name, $email];
    
    
    if (!empty($new_password)) {
        $password_to_save = $new_password; 
        $update_fields[] = 'password = ?';
        $update_values[] = $password_to_save;
    }
    
    
    $sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = ?";
    $update_values[] = $user_id;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($update_values);
        
        
        $_SESSION['user_name'] = $name;

        $message = "<div class='alert alert-success'>Profile updated successfully!</div>";
    } catch (PDOException $e) {
        
        if ($e->getCode() == '23000') { 
             $message = "<div class='alert alert-danger'>Error: That email is already in use.</div>";
        } else {
             $message = "<div class='alert alert-danger'>An unexpected error occurred.</div>";
        }
    }
}

// Fetch Current User Data 
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: logout.php');
    exit;
}
include 'user_header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
        <h2 class="mb-4">My Profile</h2>
        
        <?= $message ?>

        <div class="card shadow-sm" style="max-width: 600px;">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password (Leave blank to keep current)</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                        <small class="form-text text-muted">Must be at least 6 characters.</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
include 'footer.php';
?>