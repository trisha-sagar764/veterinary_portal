<?php
// includes/auth/validate_session.php
if (empty($_SESSION['pet_owner_id']) || empty($_SESSION['logged_in'])) {
    header('Location: login.php?reason=not_logged_in');
    exit;
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}