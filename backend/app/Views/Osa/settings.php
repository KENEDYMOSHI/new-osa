<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'License Settings' ?></title>
    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Inter', sans-serif;
            color: #1f2937;
        }
        .header-section {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .content-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="header-section">
        <h1 class="h4 mb-0">License Settings</h1>
        <a href="/" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="container">
        <div class="content-card">
            <h4>License Configuration</h4>
            <p class="text-muted">Configure license types, fees, and approval workflows here.</p>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i> This module is currently under development.
            </div>

            <!-- Placeholder for future settings -->
            <form>
                <div class="form-group">
                    <label>Default License Validity (Months)</label>
                    <input type="number" class="form-control" value="12" readonly>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
