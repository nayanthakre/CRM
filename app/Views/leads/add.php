
<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../../../config/database.php';

function escapeHtml($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$name = '';
$phone = '';
$email = '';
$source = 'Website';
$status = 'new';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $source = trim($_POST['source'] ?? 'Website');
    $status = $_POST['status'] ?? 'new';

    $allowedStatuses = ['new', 'contacted', 'qualified', 'converted', 'lost'];

    if ($name === '' || $phone === '') {
        $error = 'Name and phone are required.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!in_array($status, $allowedStatuses, true)) {
        $error = 'Invalid lead status.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO leads (name, phone, email, source, status, assigned_to)
                 VALUES (:name, :phone, :email, :source, :status, :assigned_to)"
            );

            $stmt->execute([
                'name' => $name,
                'phone' => $phone,
                'email' => $email !== '' ? $email : null,
                'source' => $source !== '' ? $source : 'Website',
                'status' => $status,
                'assigned_to' => $_SESSION['user_id']
            ]);

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {
            $error = 'Unable to save this lead. Please try again.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Lead | State CRM</title>
    <link rel="stylesheet" href="../../../public/assets/css/dashboard.css">

    <style>
        .form-card {
            max-width: 760px;
            margin: 0 auto;
            padding: 28px;
            background: #fff;
            border: 1px solid #e8ecf2;
            border-radius: 14px;
        }

        .form-card h2 { margin-top: 0; }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .form-group { margin-bottom: 18px; }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #dce2eb;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 10px;
        }

        .secondary-button {
            padding: 13px 18px;
            border: 1px solid #dce2eb;
            border-radius: 9px;
        }

        .error-message {
            padding: 12px;
            margin-bottom: 20px;
            background: #fff0f0;
            color: #b42318;
            border-radius: 8px;
        }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; gap: 0; }
            .form-card { padding: 18px; }
        }
    </style>
</head>
<body>

<main class="main-content">
    <a href="index.php" style="display:inline-block;margin-bottom:20px;color:#3157d5;">
        ← Back to Leads
    </a>

    <section class="form-card">
        <h2>Add New Lead</h2>
        <p style="color:#7b8798;margin-bottom:24px;">
            Enter the details of a new customer enquiry.
        </p>

        <?php if ($error !== ''): ?>
            <div class="error-message"><?= escapeHtml($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Customer Name *</label>
                    <input id="name" name="name" type="text"
                           maxlength="100" required
                           value="<?= escapeHtml($name) ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input id="phone" name="phone" type="tel"
                           maxlength="20" required
                           value="<?= escapeHtml($phone) ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email"
                           maxlength="150"
                           value="<?= escapeHtml($email) ?>">
                </div>

                <div class="form-group">
                    <label for="source">Lead Source</label>
                    <select id="source" name="source">
                        <?php foreach (['Website', 'Facebook', 'Referral', 'Phone', 'Walk-in', 'Other'] as $option): ?>
                            <option value="<?= escapeHtml($option) ?>"
                                <?= $source === $option ? 'selected' : '' ?>>
                                <?= escapeHtml($option) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Lead Status</label>
                    <select id="status" name="status">
                        <?php foreach (['new', 'contacted', 'qualified', 'converted', 'lost'] as $option): ?>
                            <option value="<?= escapeHtml($option) ?>"
                                <?= $status === $option ? 'selected' : '' ?>>
                                <?= escapeHtml(ucfirst($option)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button class="primary-button" type="submit">
                    Save Lead
                </button>

                <a class="secondary-button" href="index.php">
                    Cancel
                </a>
            </div>
        </form>
    </section>
</main>

</body>
</html>
