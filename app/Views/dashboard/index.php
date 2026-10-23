
<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

function escapeHtml($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | State CRM</title>
</head>
<body>

    <h1>Welcome to State CRM</h1>

    <p>
        Hello,
        <?= escapeHtml($_SESSION['user_name']) ?>!
    </p>

    <p>
        Your email:
        <?= escapeHtml($_SESSION['user_email']) ?>
    </p>

    <p>
        Your role:
        <?= escapeHtml($_SESSION['user_role']) ?>
    </p>

    <h2>Dashboard</h2>
    <p>Our real estate CRM starts here.</p>

    <a href="../../../logout.php">Logout</a>

</body>
</html>
