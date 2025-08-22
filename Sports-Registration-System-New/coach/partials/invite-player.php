<?php
require_once '../includes/db_con.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_session();
check_role('coach');

$coach_user = get_user_by_username($_SESSION['user_login']);
$coach = get_coach_by_user_id($coach_user['id']);

if (isset($_POST['invite_player'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $player_id = $_POST['player_id'];
    $team_id = $coach['team_id'];

    $stmt = $db_con->prepare("INSERT INTO `team_invitations` (`coach_id`, `player_id`, `team_id`, `status`) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("iii", $coach['id'], $player_id, $team_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Invitation sent successfully!";
    } else {
        $_SESSION['error'] = "Failed to send invitation.";
    }
    $stmt->close();
    redirect('dashboard.php?page=invite-player');
}

?>

<h1 class="text-primary"><i class="fas fa-user-plus"></i> Invite Player</h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Invite Player</li>
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
<?php if (isset($_SESSION['error'])) : ?>
    <div class="alert alert-danger">
        <?php
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        ?>
    </div>
<?php endif; ?>

<table class="table table-striped table-hover table-bordered" id="data">
    <thead class="thead-dark">
        <tr>
            <th>Player Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Sport</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT p.*, s.name as sport_name FROM `players` p
                  JOIN `sports` s ON p.sport_id = s.id
                  WHERE p.team_id IS NULL
                  AND p.id NOT IN (SELECT player_id FROM team_invitations WHERE status = 'pending')";
        $stmt = $db_con->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
        ?>
            <tr>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['age']); ?></td>
                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                <td><?php echo htmlspecialchars($row['sport_name']); ?></td>
                <td>
                    <form action="" method="POST">
                        <input type="hidden" name="player_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit" name="invite_player" class="btn btn-primary btn-sm">Invite</button>
                    </form>
                </td>
            </tr>
        <?php }
        $stmt->close(); ?>
    </tbody>
</table>
