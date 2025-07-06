<?php
// logout.php
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out | Department of Animal Husbandry & Veterinary Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --govt-blue: #0066b3;
            --govt-dark-blue: #004080;
            --govt-light-blue: #e6f2ff;
            --govt-gold: #d4af37;
            --govt-cream: #f5f5f0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--govt-cream);
        }
        
        /* Header styles */
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
        
        .govt-seal {
            max-height: 80px;
        }
        
        .logout-message {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 50px auto;
            max-width: 600px;
            text-align: center;
            border-top: 5px solid var(--govt-gold);
        }
        
        .logout-message h2 {
            color: var(--govt-blue);
        }
        
        .logout-message .icon {
            font-size: 4rem;
            color: var(--govt-blue);
            margin-bottom: 20px;
        }
        
        .btn-logout {
            margin: 10px;
            padding: 10px 20px;
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
                    <img src="https://ahvs.andaman.gov.in/img/logo.png" alt="Government Emblem" class="govt-seal">
                </div>
                <div class="col-md-8 text-center">
                    <h3 style="color: var(--govt-blue); margin-bottom: 0;">डेयरी एवं पशुपालन विभाग</h3>
                    <h3 style="color: var(--govt-blue); margin-bottom: 0;">Department of Animal Husbandry & Veterinary Services</h3>
                    <h4 style="color: var(--govt-blue);">Andaman & Nicobar Administration</h4>
                </div>
                <div class="col-md-2 text-center">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/8/8b/Seal_of_Andaman_and_Nicobar_Islands.svg" alt="Andaman Logo" class="govt-seal">
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container" id="main-content">
        <div class="logout-message">
            <div class="icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h2>You have been successfully logged out</h2>
            <p class="lead">Thank you for using the Animal Husbandry & Veterinary Services Admin Portal</p>
            
            <div class="d-flex justify-content-center flex-wrap">
                <a href="index.php" class="btn btn-primary btn-logout">
                    <i class="bi bi-house-door"></i> Return to Home Page
                </a>
                <a href="admin_login.php" class="btn btn-outline-primary btn-logout">
                    <i class="bi bi-lock"></i> Admin Login
                </a>
            </div>
        </div>
    </div>

 <?php include __DIR__ . '/../includes/admin_footer.php'; ?>