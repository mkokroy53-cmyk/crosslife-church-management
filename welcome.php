<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crosslife Church Eldoret - Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(180deg, rgba(0,0,0,0.45), rgba(0,0,0,0.25)),
                        url('cr.png') center center / cover no-repeat fixed;
            color: #f8f9fa;
        }
        
        .welcome-card {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.22);
            box-shadow: 0 30px 90px rgba(0,0,0,0.25);
            border-radius: 32px;
            max-width: 680px;
            width: 100%;
        }
        
        .church-icon {
            font-size: 4rem;
            margin-bottom: 1.3rem;
            display: inline-block;
            padding: 1rem;
            border-radius: 50%;
            background: rgba(255,255,255,0.95);
            color: #6f42c1;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .church-name {
            font-size: 3rem;
            font-weight: 800;
            color: #2c1d5a;
            margin-bottom: 0.5rem;
            letter-spacing: -1px;
        }
        
        .subtitle {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 2.5rem;
            font-weight: 500;
        }
        
        .portal-btn {
            padding: 1.15rem 1.75rem;
            border: none;
            border-radius: 18px;
            font-size: 1.15rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.9rem;
            box-shadow: 0 12px 25px rgba(0,0,0,0.12);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .member-btn {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        
        .member-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 35px rgba(17, 153, 142, 0.35);
            color: white;
        }
        
        .staff-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .staff-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 35px rgba(102, 126, 234, 0.35);
            color: white;
        }
        
        .portal-btn i {
            font-size: 1.55rem;
        }
        
        .verse-container {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(255, 95, 109, 0.95) 100%);
            padding: 1.9rem;
            border-radius: 20px;
            color: white;
            margin-top: 2rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        
        .verse-reference {
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            opacity: 0.95;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .verse-text {
            font-size: 1rem;
            line-height: 1.8;
            font-style: italic;
            font-weight: 400;
        }
        
        @media (max-width: 576px) {
            .welcome-card {
                margin: 1rem;
            }
            
            .church-name {
                font-size: 2rem;
            }
            
            .portal-btn {
                padding: 1rem 1.5rem;
                font-size: 1.1rem;
            }
            
            .verse-text {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100 p-3">
        <div class="welcome-card card">
            <div class="card-body">
                <div class="church-icon">⛪</div>
                <h1 class="church-name display-4 fw-bold">CROSSLIFE CHURCH ELDORET</h1>
                <p class="subtitle lead text-muted">Welcome to our Church Management Portal</p>
                
                <div class="d-grid gap-3 mb-4">
                    <a href="member_login.php" class="portal-btn member-btn btn">
                        <i class="fas fa-users"></i>
                        <span>Member Portal</span>
                    </a>
                    
                    <a href="staff_login.php" class="portal-btn staff-btn btn">
                        <i class="fas fa-user-shield"></i>
                        <span>Staff Login</span>
                    </a>
                </div>
                
                <div class="verse-container rounded">
                    <div class="verse-reference small fw-semibold">JEREMIAH 29:11</div>
                    <div class="verse-text fst-italic">
                        "For I know the plans I have for you," declares the Lord, "plans to prosper you and not to harm you, plans to give you hope and a future."
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
