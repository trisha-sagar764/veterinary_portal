<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department of Animal Husbandry & Veterinary Services | A&N Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --govt-blue: #0066b3;
            --govt-gold: #ffcc00;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 16px;
        }
        
        .header {
            background-color: var(--govt-blue);
            color: white;
            padding: 8px 0;
            border-bottom: 4px solid var(--govt-gold);
        }
        
        .logo-section {
            background: white;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .nav-main {
            background-color: var(--govt-blue);
        }
        
        .nav-main .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 10px 20px;
        }
        
        .nav-main .nav-link:hover {
            background-color: #004f8a;
        }
        
        .govt-seal {
            max-height: 80px;
        }
        
        .footer {
            flex-shrink: 0;
             width: 100%;
            background-color: var(--govt-blue);
            color: white;
            padding: 30px 0 10px;
            margin-top: 30px;
        }
        
        .quick-links a {
            color: white;
            text-decoration: none;
            display: block;
            margin-bottom: 8px;
        }
        
        .registration-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin: 15px auto;
            max-width: 700px;
        }
        
        .required-field::after {
            content: " *";
            color: red;
        }
        
        .form-section {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .accessibility-bar {
            background-color: #f8f9fa;
            padding: 5px 0;
            border-bottom: 1px solid #dee2e6;
            font-size: 14px;
        }
        
        .username-status {
            margin-top: 5px;
            font-size: 0.9rem;
        }
        
        .password-requirements {
            margin-top: 0.25rem;
            padding: 0.25rem 0.5rem;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 0.8rem;
            line-height: 1.3;
        }
        
        .password-requirements li {
            position: relative;
            padding-left: 1.2rem;
            margin-bottom: 0.1rem;
            font-size: 0.8rem;
        }
        
        .password-requirements li:before {
            position: absolute;
            left: 0.1rem;
            font-size: 0.7rem;
            top: 0.1rem;
        }
        
        .password-requirements li.valid:before {
            content: "✓";
            color: #28a745;
        }
        
        .password-requirements li.invalid:before {
            content: "✗";
            color: #dc3545;
        }
        
        .otp-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        /* Accessibility Bar Styles */
        .accessibility-bar {
            background-color: #f8f9fa;
            padding: 5px 0;
            border-bottom: 1px solid #dee2e6;
            font-size: 14px;
        }
        .accessibility-items {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 15px;
        }
        .accessibility-link {
            color: #495057;
            text-decoration: none;
            cursor: pointer;
        }
        .accessibility-link:hover {
            color: #0d6efd;
        }
        .divider {
            width: 1px;
            height: 20px;
            background-color: #dee2e6;
        }
        /* Login Dropdown Styles */
.btn-outline-light {
    border-color: rgba(255,255,255,0.5);
    color: white;
}

.btn-outline-light:hover {
    background-color: rgba(255,255,255,0.1);
    border-color: white;
}

.dropdown-menu {
    min-width: 200px;
}

.dropdown-item {
    padding: 8px 16px;
}

.dropdown-item i {
    margin-right: 8px;
    width: 18px;
    text-align: center;
}
    </style>
</head>
<body>
    <!-- Accessibility Bar -->
    <div class="accessibility-bar">
        <div class="container">
            <div class="accessibility-items">
                <a href="#main-content" class="accessibility-link">SKIP TO MAIN CONTENT</a>
                <div class="divider"></div>
                <a href="#" class="accessibility-link" onclick="increaseFontSize()">A+</a>
                <a href="#" class="accessibility-link" onclick="normalFontSize()">A</a>
                <a href="#" class="accessibility-link" onclick="decreaseFontSize()">A-</a>
                <div class="divider"></div>
                <a href="#" class="accessibility-link">ENGLISH</a>
            </div>
        </div>
    </div>
    
    <!-- Top Header Strip -->
    <div class="header">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <marquee behavior="scroll" direction="left" scrollamount="3">
                        Department of Animal Husbandry & Veterinary Services | Andaman & Nicobar Administration
                    </marquee>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="text-white me-3"><i class="bi bi-telephone"></i> Helpline: 03192-238881</a>
                    <a href="#" class="text-white"><i class="bi bi-envelope"></i> dir-ah[at]and[dot]nic[dot]in</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Logo and Government Identity Section -->
    <div class="logo-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <img src="assets/images/Government logo.png" alt="Government Emblem" class="govt-seal">
                </div>
                <div class="col-md-8 text-center">
                    <h3 style="color: var(--govt-blue); margin-bottom: 0;">डेयरी एवं पशुपालन विभाग</h3>
                    <h3 style="color: var(--govt-blue); margin-bottom: 0;">Department of Animal Husbandry & Veterinary Services</h3>
                    <h4 style="color: var(--govt-blue);">Andaman & Nicobar Administration</h4>
                </div>
                <div class="col-md-2 text-center">
                    <img src="assets/images/logo.png" alt="Andaman Logo" class="govt-seal">
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg nav-main">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php"><i class="bi bi-info-circle"></i> About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="locate.php"><i class="bi bi-geo"></i> Locate Us</a></li>
                <li class="nav-item"><a class="nav-link" href="Vaccination.php"><i class="bi bi-syringe"></i> Vaccination</a></li>
                <li class="nav-item"><a class="nav-link" href="gallery.php"><i class="bi bi-images"></i> Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php"><i class="bi bi-telephone"></i> Contact</a></li>
            </ul>
            
            <!-- Improved Login Section -->
            <div class="d-flex align-items-center ms-lg-4">  <!-- Added margin on large screens -->
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center" 
                                type="button" id="userDropdown" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i>
                            <span><?= htmlspecialchars($_SESSION['user']['username'] ?? 'Account') ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center" 
                                type="button" id="loginDropdown" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            <span>Login</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="loginDropdown">
                            <li><a class="dropdown-item" href="doctor_login.php"><i class="bi bi-heart-pulse me-2"></i> Doctor</a></li>
                            <li><a class="dropdown-item" href="staff_login.php"><i class="bi bi-people me-2"></i> Staff</a></li>
                            <li><a class="dropdown-item" href="admin_login.php"><i class="bi bi-shield-lock me-2"></i> Admin</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>