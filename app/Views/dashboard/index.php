
<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../../../config/database.php';

// Fetch total leads from MySQL
$stmt = $pdo->query("SELECT COUNT(*) FROM leads");
$totalLeads = (int) $stmt->fetchColumn();



$totalProperties = (int) $pdo
    ->query("SELECT COUNT(*) FROM properties")
    ->fetchColumn();

$totalFollowUps = (int) $pdo
    ->query("SELECT COUNT(*) FROM follow_ups WHERE status = 'pending'")
    ->fetchColumn();

$totalBookings = (int) $pdo
    ->query("SELECT COUNT(*) FROM bookings WHERE status != 'cancelled'")
    ->fetchColumn();


function escapeHtml($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

$userName = $_SESSION['user_name'] ?? 'User';
$userRole = $_SESSION['user_role'] ?? 'salesperson';

$initial = strtoupper(substr($userName, 0, 1));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Dashboard | State CRM</title>

    <link rel="stylesheet"
          href="../../../public/asset/css/dashboard.css">
</head>
<body>

<div class="app-layout">

    <!-- Sidebar -->
    <aside class="sidebar">

        <div class="brand">
            <div class="brand-icon">S</div>

            <div>
                <h2>State CRM</h2>
                <p>Real Estate Management</p>
            </div>
        </div>

        <p class="menu-label">MAIN MENU</p>

        <nav class="navigation">
            <a href="index.php" class="nav-link active">
                <span>▦</span> Dashboard
            </a>

            <a href="#" class="nav-link">
                <span>♙</span> Leads
            </a>

            <a href="#" class="nav-link">
                <span>♧</span> Customers
            </a>

            <a href="#" class="nav-link">
                <span>⌂</span> Properties
            </a>

            <a href="#" class="nav-link">
                <span>◷</span> Follow-ups
            </a>

            <a href="#" class="nav-link">
                <span>▤</span> Bookings
            </a>
        </nav>

        <div class="sidebar-bottom">
            <p>Logged in as</p>
            <strong><?= escapeHtml($userRole) ?></strong>

            <a href="../../../logout.php" class="logout-link">
                ↪ Logout
            </a>
        </div>

    </aside>

    <!-- Main content -->
    <main class="main-content">

        <header class="topbar">
            <div>
                <p class="breadcrumb">Workspace / Overview</p>
                <h1>Dashboard</h1>
            </div>

            <div class="profile">
                <div class="avatar">
                    <?= escapeHtml($initial) ?>
                </div>

                <div>
                    <strong><?= escapeHtml($userName) ?></strong>
                    <p><?= escapeHtml($userRole) ?></p>
                </div>
            </div>
        </header>

        <section class="welcome-section">
            <div>
                <p class="eyebrow">YOUR BUSINESS AT A GLANCE</p>

                <h2>Welcome back, <?= escapeHtml($userName) ?>!</h2>

                <p>
                    Manage your properties, leads and customer
                    relationships from one place.
                </p>
            </div>

            <a href="#" class="primary-button">
                + Add New Lead
            </a>
        </section>

        <!-- Summary cards: placeholder values for now -->
        <section class="stats-grid">

            <article class="stat-card">
                <div class="stat-heading">
                    <span>Total Leads</span>
                    <span class="stat-icon blue">L</span>
                </div>

                <h3><?= $totalLeads ?></h3>
                <p>Lead tracking starts here</p>
            </article>

            <article class="stat-card">
                <div class="stat-heading">
                    <span>Properties</span>
                    <span class="stat-icon purple">P</span>
                </div>

                <h3><?= $totalProperties ?></h3>
                <p>Manage property listings</p>
            </article>

            <article class="stat-card">
                <div class="stat-heading">
                    <span>Follow-ups</span>
                    <span class="stat-icon orange">F</span>
                </div>

                <h3><?= $totalFollowUps ?></h3>
                <p>Track upcoming calls</p>
            </article>

            <article class="stat-card">
                <div class="stat-heading">
                    <span>Bookings</span>
                    <span class="stat-icon green">B</span>
                </div>

                <h3><?= $totalBookings ?></h3>
                <p>Track successful deals</p>
            </article>

        </section>

        <section class="content-grid">

            <article class="panel">
                <div class="panel-heading">
                    <div>
                        <h3>Recent Leads</h3>
                        <p>Your latest customer enquiries</p>
                    </div>

                    <a href="#">View all</a>
                </div>

                <div class="empty-state">
                    <div class="empty-icon">♙</div>
                    <strong>No leads yet</strong>
                    <p>
                        New customer enquiries will appear here
                        when you start adding leads.
                    </p>
                </div>
            </article>

            <article class="panel">
                <div class="panel-heading">
                    <div>
                        <h3>Quick Actions</h3>
                        <p>Common tasks</p>
                    </div>
                </div>

                <div class="quick-actions">
                    <a href="#" class="action-item">
                        <span class="action-symbol blue">+</span>
                        <span>
                            <strong>Add a lead</strong>
                            <small>Record a new enquiry</small>
                        </span>
                        <span>→</span>
                    </a>

                    <a href="#" class="action-item">
                        <span class="action-symbol purple">⌂</span>
                        <span>
                            <strong>Add a property</strong>
                            <small>Create a listing</small>
                        </span>
                        <span>→</span>
                    </a>

                    <a href="#" class="action-item">
                        <span class="action-symbol orange">◷</span>
                        <span>
                            <strong>Schedule follow-up</strong>
                            <small>Plan your next call</small>
                        </span>
                        <span>→</span>
                    </a>
                </div>
            </article>

        </section>

        <footer class="footer">
            State CRM &copy; <?= date('Y') ?>.
            Real Estate Management System.
        </footer>

    </main>

</div>

</body>
</html>
