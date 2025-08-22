<?php
require_once '../includes/db_con.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_session();
check_role('coach');

$coach_user = get_user_by_username($_SESSION['user_login']);
$coach = get_coach_by_user_id($coach_user['id']);
$team_id = $coach['team_id'];

if (isset($_POST['approve_request'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $request_id = $_POST['request_id'];
    $player_id = $_POST['player_id'];

    // Update player's team
    $update_player_stmt = $db_con->prepare("UPDATE `players` SET `team_id` = ? WHERE `id` = ?");
    $update_player_stmt->bind_param("ii", $team_id, $player_id);
    $update_player_stmt->execute();
    $update_player_stmt->close();

    // Update request status
    $update_request_stmt = $db_con->prepare("UPDATE `team_join_requests` SET `status` = 'approved' WHERE `id` = ?");
    $update_request_stmt->bind_param("i", $request_id);
    $update_request_stmt->execute();
    $update_request_stmt->close();

    $_SESSION['message'] = "Player has been added to your team.";
    redirect('dashboard.php?page=manage-join-requests');
}

if (isset($_POST['reject_request'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $request_id = $_POST['request_id'];

    $update_request_stmt = $db_con->prepare("UPDATE `team_join_requests` SET `status` = 'rejected' WHERE `id` = ?");
    $update_request_stmt->bind_param("i", $request_id);
    $update_request_stmt->execute();
    $update_request_stmt->close();

    $_SESSION['message'] = "Request has been rejected.";
    redirect('dashboard.php?page=manage-join-requests');
}

?>

<h1 class="text-primary"><i class="fas fa-tasks"></i> Manage Join Requests</h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Manage Join Requests</li>
    </ol>
</nav>

<?php if (isset($_SESSION['message'])) : ?>
    <div class="alert alert-success">
        <?php
        echo $_SESSION['message'];
        unset($_SESSION['message']);
        ?>
    </div>
<?php endif; ?>

<table class="table table-striped table-hover table-bordered" id="data">
    <thead class="thead-dark">
        <tr>
            <th>Player Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT tjr.id, tjr.player_id, p.full_name, p.age, p.gender FROM `team_join_requests` tjr
                  JOIN `players` p ON tjr.player_id = p.id
                  WHERE tjr.team_id = ? AND tjr.status = 'pending'";
        $stmt = $db_con->prepare($query);
        $stmt->bind_param("i", $team_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
        ?>
            <tr>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['age']); ?></td>
                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                <td>
                    <form action="" method="POST" style="display: inline-block;">
                        <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="player_id" value="<?php echo $row['player_id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit" name="approve_request" class="btn btn-success btn-sm">Approve</button>
                    </form>
                    <form action="" method="POST" style="display: inline-block;">
                        <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit" name="reject_request" class="btn btn-danger btn-sm">Reject</button>
                    </form>
                </td>
            </tr>
        <?php }
        $stmt->close(); ?>
    </tbody>
</table>
