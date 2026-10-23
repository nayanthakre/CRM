
<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../../../config/database.php';

function escapeHtml($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

$stmt = $pdo->query(
    "SELECT id, name, phone, email, source, status, created_at
     FROM leads
     ORDER BY id DESC"
);

$leads = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Leads | State CRM</title>

    <link rel="stylesheet" href="../../../public/assets/css/dashboard.css">

    <style>
        .leads-container {
            background: white;
            border: 1px solid #e8ecf2;
            border-radius: 14px;
            padding: 24px;
            overflow-x: auto;
        }

        .leads-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .leads-heading h2 {
            margin: 0 0 8px;
        }

        .leads-heading p {
            color: #7b8798;
            margin: 0;
        }

        .leads-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        .leads-table th,
        .leads-table td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #e8ecf2;
        }

        .leads-table th {
            color: #7b8798;
            background: #f8faff;
            font-size: 12px;
        }

        .leads-table td {
            font-size: 13px;
        }

        .lead-status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            background: #edf2ff;
            color: #3157d5;
            text-transform: capitalize;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #3157d5;
        }
    </style>
</head>

<body>

<main class="main-content">

    <a class="back-link" href="../dashboard/index.php">
        ← Back to Dashboard
    </a>

    <section class="leads-container">

    <div class="leads-heading">
    <div>
        <h2>Leads Management</h2>
        <p>View all customer enquiries in one place.</p>
    </div>

    <div style="display:flex;align-items:center;gap:16px;">
        <strong>Total Leads: <?= count($leads) ?></strong>
        <a href="add.php" class="primary-button">+ Add New Lead</a>
    </div>
</div>

        <table class="leads-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($leads) > 0): ?>

                    <?php foreach ($leads as $lead): ?>
                        <tr>
                            <td><?= (int) $lead['id'] ?></td>
                            <td><?= escapeHtml($lead['name']) ?></td>
                            <td><?= escapeHtml($lead['phone']) ?></td>
                            <td><?= escapeHtml($lead['email'] ?? '') ?></td>
                            <td><?= escapeHtml($lead['source']) ?></td>
                            <td>
                                <span class="lead-status">
                                    <?= escapeHtml($lead['status']) ?>
                                </span>
                            </td>
                            <td><?= escapeHtml($lead['created_at']) ?></td>
                            <td style="white-space:nowrap;">
    <a href="edit.php?id=<?= (int) $lead['id'] ?>"
       style="color:#3157d5;">Edit</a>

    <form method="POST" action="delete.php"
          onsubmit="return confirm('Are you sure you want to delete this lead?');"
          style="display:inline;margin-left:12px;">
        <input type="hidden" name="id"
               value="<?= (int) $lead['id'] ?>">
        <button type="submit"
                style="border:0;background:none;color:#dc4444;cursor:pointer;">
            Delete
        </button>
    </form>
</td>
                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="8">No leads found.</td>
                    </tr>

                <?php endif; ?>
            </tbody>
        </table>

    </section>

</main>

</body>
</html>
