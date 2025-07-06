<?php
session_start();
require_once  'C:\xampp\htdocs\veterinary_portal\includes\config.php';
require_once 'C:\xampp\htdocs\veterinary_portal\includes\csrf.php';
require_once 'C:\xampp\htdocs\veterinary_portal\includes\functions.php';

$login_error = '';

// CAPTCHA Generation
if (!isset($_SESSION['captcha'])) {
    $_SESSION['captcha'] = substr(md5(rand()), 0, 6); // 6-character random string
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Verify CAPTCHA first
    if (empty($_POST['captcha']) || $_POST['captcha'] !== $_SESSION['captcha']) {
        $login_error = "Invalid CAPTCHA code";
    } else {
        $username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Basic validation
if (empty($username) || empty($password)) {
    $login_error = "Username and password are required";
}
        try {
            $db = getDatabaseConnection();
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $stmt = $db->prepare("SELECT * FROM pet_owners WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user;
    $_SESSION['pet_owner_id'] = $user['pet_owner_id']; // Add this
    $_SESSION['logged_in'] = true; // Add this
    header("Location: dashboard.php");
    exit;

            } else {
                $login_error = "Invalid username or password";
            }
        } catch(PDOException $e) {
            $login_error = "Login error. Please try again.";
        }
    }
    // Regenerate CAPTCHA after each attempt
    $_SESSION['captcha'] = substr(md5(rand()), 0, 6);
}
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
        <style>     
        /* Login Error Styles */
        .login-error {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        /* Password Field Styles */
        .password-field {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        
        /* CAPTCHA Styles */
        .captcha-container {
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .captcha-image {
            border: 1px solid #ddd;
            height: 50px;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            font-size: 24px;
            letter-spacing: 3px;
            font-weight: bold;
            color: #333;
            padding: 5px;
            border-radius: 4px;
        }
        .captcha-refresh-btn {
            cursor: pointer;
            color: var(--govt-blue);
        }
    </style>
</head>
    <!-- Main Content -->
    <div class="container mt-4" id="main-content">
        <div class="row">
            <!-- Left Column (3/4 width) -->
            <div class="col-lg-9">
                <!-- Image Slider -->
                <div class="slider-section">
                    <div id="mainSlider" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="https://ahvs.andaman.gov.in/admin-pannel/sliderupload/pet%20walk.jpg" class="d-block w-100" alt="Veterinary Services">
                            </div>
                            <div class="carousel-item">
                                <img src="https://ahvs.andaman.gov.in/admin-pannel/sliderupload/duck.jpg" class="d-block w-100" alt="Animal Care">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>

                <!-- Announcements -->
                <div class="mt-4">
                    <h4 class="mb-3" style="color: var(--govt-blue); border-bottom: 2px solid var(--govt-blue); padding-bottom: 5px;">
                        <i class="bi bi-megaphone"></i> Announcements
                    </h4>
                    
                    <div class="announcement">
                        <h5>Pet Registration Drive</h5>
                        <p>All pet owners are requested to register their pets with the department before 30th November 2023.</p>
                        <small class="text-muted">Posted on: 15-10-2023</small>
                    </div>
                    
                    <div class="announcement">
                        <h5>Free Vaccination Camp</h5>
                        <p>Free rabies vaccination camp will be held at Port Blair Veterinary Hospital on 25th October 2023.</p>
                        <small class="text-muted">Posted on: 10-10-2023</small>
                    </div>
                </div>
            </div>

            <!-- Right Column (1/4 width) -->
<div class="col-lg-3">
    <!-- Login Box -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header" style="background-color: var(--govt-blue); color: white;">
            <h5 class="mb-0 text-center"><i class="bi bi-person-circle"></i> Pet Owner Login</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-field">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <span class="password-toggle" onclick="togglePassword('password')">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>
                        
                        <!-- CAPTCHA Section -->
                        <div class="mb-3 captcha-container">
                            <label for="captcha" class="form-label">Enter CAPTCHA Code</label>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="captcha-image">
                                    <?php echo $_SESSION['captcha']; ?>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary captcha-refresh-btn" onclick="refreshCaptcha()">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control" id="captcha" name="captcha" required>
                        </div>
                        
                        <?php if ($login_error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($login_error); ?></div>
                        <?php endif; ?>
                        
                        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                    </form>
                    <div class="text-center mt-2">
                        <a href="registration.php" class="small">New User? Register Here</a>
                        <br>
                        <a href="forgot_password.php" class="small">Forgot Password?</a>
                    </div>
                </div>
                        </div>
                <!-- Quick Links -->
                <div class="card mb-4">
                    <div class="card-header" style="background-color: var(--govt-blue); color: white;">
                        <i class="bi bi-link-45deg"></i> Quick Links
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><a href="registration.php" class="text-decoration-none"><i class="bi bi-chevron-right"></i> Pet Registration</a></li>
                            <li><a href="Vaccination Schedule.php" class="text-decoration-none"><i class="bi bi-chevron-right"></i> Vaccination Schedule</a></li>
                            <li><a href="locate.php" class="text-decoration-none"><i class="bi bi-chevron-right"></i> Find Veterinary Center</a></li>
                            <li><a href="#" class="text-decoration-none"><i class="bi bi-chevron-right"></i> Acts & Rules</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Font size adjustment functions
        function increaseFontSize() {
            document.body.style.fontSize = '20px';
        }
        function normalFontSize() {
            document.body.style.fontSize = '16px';
        }
        function decreaseFontSize() {
            document.body.style.fontSize = '14px';
        }
        
        // Skip to main content functionality
        document.querySelector('a[href="#main-content"]').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('main-content').setAttribute('tabindex', '-1');
            document.getElementById('main-content').focus();
        });
        
        // Password visibility toggle
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
        
        // Refresh CAPTCHA
        function refreshCaptcha() {
    fetch('refresh_captcha.php')
        .then(response => response.json())  // Change from .text() to .json()
        .then(data => {
            document.querySelector('.captcha-image').textContent = data.captcha;
        });

        }
    </script>
</body>
</html>