<?php
require_once '../includes/db_con.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_session();
check_role('admin');

if (isset($_GET['id'])) {
    $user_id = base64_decode($_GET['id']);
    $stmt = $db_con->prepare("SELECT p.photo, p.full_name FROM `players` p WHERE p.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $player = $result->fetch_assoc();
    $stmt->close();
} else {
    redirect('index.php?page=all-users');
}

$page_title = "View Player Document";
require_once '../templates/header.php';
?>

<div class="container">
    <h1 class="text-primary"><i class="fas fa-file-alt"></i> View Document for <?php echo htmlspecialchars($player['full_name']); ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item" aria-current="page"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item" aria-current="page"><a href="index.php?page=all-users">All Users</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Document</li>
        </ol>
    </nav>
    <hr>

    <?php if ($player && !empty($player['photo'])) : ?>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4>Player ID Picture</h4>
                    </div>
                    <div class="card-body text-center">
                        <img src="../uploads/<?php echo htmlspecialchars($player['photo']); ?>" alt="Player Document" class="img-fluid" style="max-width: 500px; border: 1px solid #ddd;">
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="alert alert-warning">No document found for this player.</div>
    <?php endif; ?>

    <div class="text-center mt-3">
        <a href="index.php?page=all-users" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to All Users</a>
    </div>
</div>

<?php require_once '../templates/footer.php'; ?>
