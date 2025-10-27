<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --custom-blue: #424fa3; 
        }
        
        .nav-link { font-weight: 500; }
        .table td, .table th { vertical-align: middle; }
        .navbar-dark {
            background-color: transparent !important; 
        }

        .navbar-dark.bg-dark {
           
            background: linear-gradient(135deg, #e0f7fa 0%, var(--custom-blue) 100%) !important;
            color: #000; 
        }
        
    
        .navbar-dark .navbar-brand,
        .navbar-dark .navbar-nav .nav-link,
        .navbar-dark .navbar-text {
            color: #000 !important; 
        }

       
        .navbar-dark .navbar-nav .nav-link:hover,
        .navbar-dark .navbar-nav .nav-link:focus,
        .navbar-dark .navbar-text strong {
            color: #120585 !important; 
        }
        
       
        .navbar-dark .btn-outline-light {
            color: #000;
            border-color: #000;
        }
        .navbar-dark .btn-outline-light:hover {
            color: #fff;
            background-color: var(--custom-blue);
            border-color: var(--custom-blue);
        }

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="admin_dashboard.php">Admin Panel</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_dashboard.php">Projects</a>
                    </li>
                </ul>
                <span class="navbar-text me-3">
                    Logged in as: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
                </span>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>
    <main>