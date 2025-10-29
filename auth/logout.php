
<?php
// logout.php
// Place this file in the auth/ folder
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to index page
header('Location: /trimbook/index.php');
exit;
?>
