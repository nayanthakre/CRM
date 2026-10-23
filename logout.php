
<?php

session_start();

$_SESSION = [];

session_destroy();

header('Location: app/Views/auth/login.php');
exit;
