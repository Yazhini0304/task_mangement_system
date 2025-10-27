
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --custom-blue: #424fa3; 
            --custom-blue-hover: #323d83;
            --custom-dark-text: #1a237e;
        }
        body {
            background-color: #f4f7f9;
        }
        .navbar-light.bg-light {
            background: linear-gradient(135deg, #e0f7fa 0%, var(--custom-blue) 100%) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .navbar-light .navbar-brand,
        .navbar-light .navbar-nav .nav-link,
        .navbar-light .navbar-text {
            color: var(--custom-dark-text) !important; 
        }

        .navbar-light .navbar-nav .nav-link.active,
        .navbar-light .navbar-nav .nav-link:hover {
            color: #fff !important; 
            background-color: var(--custom-blue-hover);
            border-radius: 5px;
            padding: 8px 12px;
        }
      
        .btn-outline-danger {
            --bs-btn-color: var(--custom-blue);
            --bs-btn-border-color: var(--custom-blue);
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: var(--custom-blue);
            --bs-btn-hover-border-color: var(--custom-blue);
        }

        main {
            padding-bottom: 50px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container">
            <a class="navbar-brand" href="user/user_dashboard.php">Task Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="user_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">My Profile</a>
                    </li>
                </ul>
                <span class="navbar-text me-3">
                    Welcome, <?= $user_name ?>
                </span>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>
    <main class="container mt-5">
