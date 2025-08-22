<?php
require_once 'includes/db_con.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

check_session();

$user = get_user_by_username($_SESSION['user_login']);

$page_title = "Home";
require_once 'templates/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card mt-5">
                <div class="card-header">
                    <h2 class="text-center">Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h2>
                </div>
                <div class="card-body">
                    <p class="text-center">Your application status is: <strong><?php echo ucfirst(htmlspecialchars($user['status'])); ?></strong></p>
                    <?php if ($user['status'] === 'pending'): ?>
                        <p class="text-center">Your application is currently under review. Please check back later.</p>
                    <?php elseif ($user['status'] === 'rejected'): ?>
                        <p class="text-center">We regret to inform you that your application has been rejected. Please contact the administrator for more information.</p>
                    <?php else: ?>
                        <p class="text-center">Your application has been approved. You can now access your dashboard.</p>
                        <div class="text-center">
                            <a href="<?php echo strtolower($user['role']); ?>/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-center">
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
