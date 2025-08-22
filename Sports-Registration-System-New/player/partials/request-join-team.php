<?php
require_once '../includes/db_con.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_session();
check_role('player');

$player_user = get_user_by_username($_SESSION['user_login']);
$player = get_player_by_user_id($player_user['id']);

if (isset($_POST['request_join'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $team_id = $_POST['team_id'];

    $stmt = $db_con->prepare("INSERT INTO `team_join_requests` (`player_id`, `team_id`, `status`) VALUES (?, ?, 'pending')");
    $stmt->bind_param("ii", $player['id'], $team_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Your request to join the team has been sent.";
    } else {
        $_SESSION['error'] = "Failed to send your request.";
    }
    $stmt->close();
    redirect('dashboard.php?page=request-join-team');
}

?>

<h1 class="text-primary"><i class="fas fa-users"></i> Request to Join Team</h1>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Request to Join Team</li>
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
            <th>Team Name</th>
            <th>Coach</th>
            <th>Sport</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT t.*, c.full_name as coach_name, s.name as sport_name FROM `teams` t
                  JOIN `coaches` c ON t.coach_id = c.id
                  JOIN `sports` s ON t.sport_id = s.id
                  WHERE t.id NOT IN (SELECT team_id FROM team_join_requests WHERE player_id = ? AND status = 'pending')";
        $stmt = $db_con->prepare($query);
        $stmt->bind_param("i", $player['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
        ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['coach_name']); ?></td>
                <td><?php echo htmlspecialchars($row['sport_name']); ?></td>
                <td>
                    <form action="" method="POST">
                        <input type="hidden" name="team_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit" name="request_join" class="btn btn-primary btn-sm">Request to Join</button>
                    </form>
                </td>
            </tr>
        <?php }
        $stmt->close(); ?>
    </tbody>
</table>
