
<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../../../config/database.php';

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$allowedStatuses = ['available', 'sold', 'rented'];
$error = '';
$editProperty = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $title = trim($_POST['title'] ?? '');
        $type = trim($_POST['property_type'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $priceInput = trim($_POST['price'] ?? '');
        $status = $_POST['status'] ?? 'available';

        if (
            $title === '' ||
            $type === '' ||
            $location === '' ||
            $priceInput === '' ||
            !is_numeric($priceInput) ||
            (float) $priceInput < 0 ||
            !in_array($status, $allowedStatuses, true)
        ) {
            $error = 'Please enter valid details in all required fields.';
        } else {
            $price = (float) $priceInput;

            if ($id && $id > 0) {
                $stmt = $pdo->prepare(
                    "UPDATE properties
                     SET title = ?, property_type = ?, location = ?,
                         price = ?, status = ?
                     WHERE id = ?"
                );

                $stmt->execute([
                    $title, $type, $location, $price, $status, $id
                ]);
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO properties
                     (title, property_type, location, price, status)
                     VALUES (?, ?, ?, ?, ?)"
                );

                $stmt->execute([
                    $title, $type, $location, $price, $status
                ]);
            }

            header('Location: index.php');
            exit;
        }
    }

    if ($action === 'delete') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id && $id > 0) {
            $stmt = $pdo->prepare(
                "DELETE FROM properties WHERE id = ?"
            );
            $stmt->execute([$id]);
        }

        header('Location: index.php');
        exit;
    }
}

// Load property for editing
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);

if ($editId && $editId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
    $stmt->execute([$editId]);
    $editProperty = $stmt->fetch() ?: null;
}

// Load all properties
$stmt = $pdo->query("SELECT * FROM properties ORDER BY id DESC");
$properties = $stmt->fetchAll();

$isEditing = $editProperty !== null;

$formTitle = $isEditing ? $editProperty['title'] : '';
$formType = $isEditing ? $editProperty['property_type'] : '';
$formLocation = $isEditing ? $editProperty['location'] : '';
$formPrice = $isEditing ? $editProperty['price'] : '';
$formStatus = $isEditing ? $editProperty['status'] : 'available';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties | State CRM</title>
    <link rel="stylesheet" href="../../../public/assets/css/dashboard.css">

    <style>
        .page-wrap { max-width: 1200px; margin: auto; }
        .panel {
            background: white;
            border: 1px solid #e8ecf2;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .page-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 22px;
        }
        .page-heading h1 { margin: 0 0 8px; }
        .muted { color: #7b8798; }
        .property-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }
        .field label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .field input, .field select {
            width: 100%;
            padding: 12px;
            border: 1px solid #dce2eb;
            border-radius: 8px;
            font-size: 14px;
            background: white;
        }
        .form-actions {
            grid-column: 1 / -1;
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .cancel-link { color: #3157d5; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 720px; }
        th, td {
            padding: 14px 12px;
            border-bottom: 1px solid #e8ecf2;
            text-align: left;
            font-size: 13px;
        }
        th { background: #f8faff; color: #7b8798; }
        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            background: #edf2ff;
            color: #3157d5;
            text-transform: capitalize;
        }
        .action-link { color: #3157d5; }
        .delete-button {
            border: 0;
            background: none;
            color: #dc4444;
            cursor: pointer;
            font: inherit;
        }
        .error {
            padding: 12px;
            margin-bottom: 18px;
            border-radius: 8px;
            background: #fff0f0;
            color: #b42318;
        }
        @media (max-width: 650px) {
            .property-form { grid-template-columns: 1fr; }
            .page-heading { align-items: flex-start; flex-direction: column; }
            .panel { padding: 16px; }
        }
    </style>
</head>

<body>
<main class="main-content">
    <div class="page-wrap">

        <a href="../dashboard/index.php"
           style="display:inline-block;margin-bottom:20px;color:#3157d5;">
            ← Back to Dashboard
        </a>

        <div class="page-heading">
            <div>
                <h1>Properties Management</h1>
                <p class="muted">Add and manage your real estate listings.</p>
            </div>

            <strong>Total Properties: <?= count($properties) ?></strong>
        </div>

        <section class="panel" id="property-form">
            <h2><?= $isEditing ? 'Edit Property' : 'Add New Property' ?></h2>

            <?php if ($error !== ''): ?>
                <div class="error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="property-form">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id"
                       value="<?= $isEditing ? (int) $editProperty['id'] : '' ?>">

                <div class="field">
                    <label for="title">Property Title *</label>
                    <input id="title" name="title" maxlength="150"
                           required value="<?= e($formTitle) ?>"
                           placeholder="e.g. 2 BHK Apartment">
                </div>

                <div class="field">
                    <label for="property_type">Property Type *</label>
                    <select id="property_type" name="property_type" required>
                        <?php foreach (['Apartment', 'Flat', 'House', 'Villa', 'Commercial', 'Plot', 'Other'] as $type): ?>
                            <option value="<?= e($type) ?>"
                                <?= $formType === $type ? 'selected' : '' ?>>
                                <?= e($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="location">Location *</label>
                    <input id="location" name="location" maxlength="150"
                           required value="<?= e($formLocation) ?>"
                           placeholder="e.g. Pune, Maharashtra">
                </div>

                <div class="field">
                    <label for="price">Price (₹) *</label>
                    <input id="price" name="price" type="number"
                           min="0" step="0.01" required
                           value="<?= e($formPrice) ?>"
                           placeholder="e.g. 6500000">
                </div>

                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <?php foreach ($allowedStatuses as $status): ?>
                            <option value="<?= e($status) ?>"
                                <?= $formStatus === $status ? 'selected' : '' ?>>
                                <?= e(ucfirst($status)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="primary-button">
                        <?= $isEditing ? 'Update Property' : 'Save Property' ?>
                    </button>

                    <?php if ($isEditing): ?>
                        <a href="index.php" class="cancel-link">Cancel Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="panel">
            <h2>All Properties</h2>
            <p class="muted">Your saved property listings appear here.</p>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if ($properties): ?>
                        <?php foreach ($properties as $property): ?>
                            <tr>
                                <td><?= (int) $property['id'] ?></td>
                                <td><?= e($property['title']) ?></td>
                                <td><?= e($property['property_type']) ?></td>
                                <td><?= e($property['location']) ?></td>
                                <td>₹<?= number_format((float) $property['price'], 2) ?></td>
                                <td>
                                    <span class="status">
                                        <?= e($property['status']) ?>
                                    </span>
                                </td>
                                <td style="white-space:nowrap;">
                                    <a class="action-link"
                                       href="index.php?edit=<?= (int) $property['id'] ?>#property-form">
                                        Edit
                                    </a>

                                    <form method="POST"
                                          onsubmit="return confirm('Delete this property?');"
                                          style="display:inline;margin-left:12px;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id"
                                               value="<?= (int) $property['id'] ?>">
                                        <button class="delete-button" type="submit">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No properties found. Add your first property above.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</main>
</body>
</html>
