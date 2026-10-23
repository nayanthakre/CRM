
<?php

require_once __DIR__ . '/../../../config/database.php';

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must contain at least 8 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO users (name, email, password)
                 VALUES (:name, :email, :password)'
            );

            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => password_hash(
                    $password,
                    PASSWORD_DEFAULT
                )
            ]);

            header('Location: register.php?success=1');
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'This email is already registered.';
            } else {
                error_log($e->getMessage());
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}

$success = isset($_GET['success']);

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
    <title>Create account | State</title>
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
                <?php if ($success): ?>
                    <p class="auth-kicker">Account ready</p>
                    <h1>You're registered</h1>
                    <p class="auth-lede">Sign in with the email you just used. Your desk is empty and waiting.</p>
                    <a class="btn" href="login.php">Continue to sign in</a>
                <?php else: ?>
                    <p class="auth-kicker">Create account</p>
                    <h1>Open your desk</h1>
                    <p class="auth-lede">Name, email, and a password. That's the whole form.</p>

                    <?php if ($errors): ?>
                        <div class="alert" role="alert">
                            <p>Please fix the following:</p>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= escapeHtml($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="field">
                            <label for="name">Full name</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="<?= escapeHtml($name) ?>"
                                maxlength="100"
                                autocomplete="name"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="email">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?= escapeHtml($email) ?>"
                                maxlength="150"
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
                                    minlength="8"
                                    autocomplete="new-password"
                                    aria-describedby="password-hint"
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
                        <p class="hint" id="password-hint">At least 8 characters.</p>

                        <div class="field">
                            <label for="confirm_password">Confirm password</label>
                            <div class="field-control">
                                <input
                                    type="password"
                                    id="confirm_password"
                                    name="confirm_password"
                                    minlength="8"
                                    autocomplete="new-password"
                                    required
                                >
                                <button
                                    type="button"
                                    class="ghost-toggle"
                                    data-toggle-password
                                    aria-controls="confirm_password"
                                    aria-pressed="false"
                                >Show</button>
                            </div>
                        </div>

                        <button class="btn" type="submit">Create account</button>
                    </form>

                    <p class="auth-switch">
                        Already have an account?
                        <a href="login.php">Sign in</a>
                    </p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script src="../../../assets/js/auth.js"></script>
</body>
</html>
