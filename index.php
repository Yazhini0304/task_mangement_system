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
        }

        body {
            height: 100vh;
            background: linear-gradient(135deg, #e0f7fa 0%, var(--custom-blue) 100%); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-content {
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            max-width: 600px;
            text-align: center;
        }
        h1 {
            color: #120585;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .lead-text {
            color: #5a6270;
            margin-bottom: 30px;
            font-size: 1.2rem;
        }

        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: var(--custom-blue);
            --bs-btn-border-color: var(--custom-blue);
            --bs-btn-hover-bg: #323d83; 
            --bs-btn-hover-border-color: #323d83;
            --bs-btn-active-bg: #222b63;
            --bs-btn-active-border-color: #222b63;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="main-content">
        <h1>Task Management System</h1>
        <p class="lead-text">
            Organize, track, and manage your projects and tasks efficiently.
        </p>
        
        <a href="login.php" class="btn btn-primary btn-lg shadow-sm">
            Access Dashboard (Login)
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>