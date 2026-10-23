
<?php

session_start();

require_once __DIR__ . '/../../../config/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../dashboard/index.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Please enter a valid email and password.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT id, name, email, password, role
             FROM users
             WHERE email = :email
             LIMIT 1'
        );

        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            header('Location: ../dashboard/index.php');
            exit;
        }

        $error = 'Invalid email or password.';
    }
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
    <title>Sign in | State</title>
    <link rel="icon" href="../../../assets/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Instrument+Serif&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/auth.css">
</head>
<body>

    <main class="auth-shell">
        <section class="auth-brand">
            <p class="brand-lockup">
                <svg class="brand-mark" viewBox="0 0 32 32" aria-hidden="true">
                    <rect x="2" y="2" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.6"/>
                    <rect x="13" y="13" width="17" height="17" fill="#12241e" stroke="currentColor" stroke-width="1.6"/>
                </svg>
                State
            </p>

            <div class="brand-copy">
                <p class="brand-kicker">Real estate CRM</p>
                <p class="brand-headline">The whole office, on one quiet screen.</p>
                <ul class="brand-points">
                    <li><span>01</span> Pipeline you can actually trust</li>
                    <li><span>02</span> Clients beside every listing</li>
                    <li><span>03</span> One record the desk can share</li>
                </ul>
            </div>

            <p class="brand-foot">Built for brokerages that still pick up the phone.</p>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <p class="auth-kicker">Sign in</p>
                <h1>Welcome back</h1>
                <p class="auth-lede">Use the email on your State account.</p>

                <?php if ($error !== ''): ?>
                    <div class="alert" role="alert">
                        <p><?= escapeHtml($error) ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="field">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= escapeHtml($email) ?>"
                            autocomplete="email"
                            inputmode="email"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="field-control">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                autocomplete="current-password"
                                required
                            >
                            <button
                                type="button"
                                class="ghost-toggle"
                                data-toggle-password
                                aria-controls="password"
                                aria-pressed="false"
                            >Show</button>
                        </div>
                    </div>

                    <button class="btn" type="submit">Sign in</button>
                </form>

                <p class="auth-switch">
                    New to State?
                    <a href="register.php">Create an account</a>
                </p>
            </div>
        </section>
    </main>

    <script src="../../../assets/js/auth.js"></script>
</body>
</html>
