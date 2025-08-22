<?php
require_once '../includes/db_con.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_session();
check_role('player');

$player_user = get_user_by_username($_SESSION['user_login']);
$player = get_player_by_user_id($player_user['id']);

if (isset($_POST['accept_invitation'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $invitation_id = $_POST['invitation_id'];
    $team_id = $_POST['team_id'];

    // Update player's team
    $update_player_stmt = $db_con->prepare("UPDATE `players` SET `team_id` = ? WHERE `id` = ?");
    $update_player_stmt->bind_param("ii", $team_id, $player['id']);
    $update_player_stmt->execute();
    $update_player_stmt->close();

    // Update invitation status
    $update_invitation_stmt = $db_con->prepare("UPDATE `team_invitations` SET `status` = 'accepted' WHERE `id` = ?");
    $update_invitation_stmt->bind_param("i", $invitation_id);
    $update_invitation_stmt->execute();
    $update_invitation_stmt->close();

    $_SESSION['message'] = "You have joined the team!";
    redirect('dashboard.php?page=view-invitations');
}

if (isset($_POST['reject_invitation'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $invitation_id = $_POST['invitation_id'];

    $update_invitation_stmt = $db_con->prepare("UPDATE `team_invitations` SET `status` = 'rejected' WHERE `id` = ?");
    $update_invitation_stmt->bind_param("i", $invitation_id);
    $update_invitation_stmt->execute();
    $update_invitation_stmt->close();

    $_SESSION['message'] = "Invitation rejected.";
    redirect('dashboard.php?page=view-invitations');
}

?>

<h1 class="text-primary"><i class="fas fa-envelope"></i> View Invitations</h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">View Invitations</li>
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
            <th>Team Name</th>
            <th>Coach</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT ti.id, ti.team_id, t.name as team_name, c.full_name as coach_name FROM `team_invitations` ti
                  JOIN `teams` t ON ti.team_id = t.id
                  JOIN `coaches` c ON ti.coach_id = c.id
                  WHERE ti.player_id = ? AND ti.status = 'pending'";
        $stmt = $db_con->prepare($query);
        $stmt->bind_param("i", $player['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
        ?>
            <tr>
                <td><?php echo htmlspecialchars($row['team_name']); ?></td>
                <td><?php echo htmlspecialchars($row['coach_name']); ?></td>
                <td>
                    <form action="" method="POST" style="display: inline-block;">
                        <input type="hidden" name="invitation_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="team_id" value="<?php echo $row['team_id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit" name="accept_invitation" class="btn btn-success btn-sm">Accept</button>
                    </form>
                    <form action="" method="POST" style="display: inline-block;">
                        <input type="hidden" name="invitation_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit" name="reject_invitation" class="btn btn-danger btn-sm">Reject</button>
                    </form>
                </td>
            </tr>
        <?php }
        $stmt->close(); ?>
    </tbody>
</table>
