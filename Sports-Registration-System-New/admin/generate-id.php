<?php
require_once '../includes/db_con.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

check_session();
check_role('admin');

if (isset($_GET['id'])) {
    $player_id = base64_decode($_GET['id']);
    $stmt = $db_con->prepare("SELECT p.*, s.name as sport_name, t.name as team_name FROM `players` p JOIN `sports` s ON p.sport_id = s.id LEFT JOIN `teams` t ON p.team_id = t.id WHERE p.id = ?");
    $stmt->bind_param("i", $player_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $player = $result->fetch_assoc();
    $stmt->close();
} else {
    redirect('index.php?page=all-players');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Player ID Card - <?php echo htmlspecialchars($player['full_name']); ?></title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/id-card.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        @media print {
            body {
                background-color: #fff;
                -webkit-print-color-adjust: exact; /* For Chrome, Safari */
                color-adjust: exact; /* For Firefox */
            }
            .container {
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .id-card {
                box-shadow: none;
                border: 1px solid #ccc;
                margin: 0 auto;
                page-break-inside: avoid;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="id-card mx-auto">
            <div class="id-card-header">
            <img src="../images/logo.png" alt="Logo" class="logo">
                <h2>Player Identification Card</h2>
            </div>
            <div class="id-card-body">
                <div class="id-card-photo">
                    <img src="../uploads/<?php echo htmlspecialchars($player['photo']); ?>" alt="Player Photo">
                </div>
                <div class="id-card-info">
                    <p><strong>Name:</strong> <?php echo ucwords(htmlspecialchars($player['full_name'])); ?></p>
                    <p><strong>Player ID:</strong> <?php echo htmlspecialchars($player['player_id']); ?></p>
                    <p><strong>Sport:</strong> <?php echo ucwords(htmlspecialchars($player['sport_name'])); ?></p>
                    <p><strong>Team:</strong> <?php echo ucwords(htmlspecialchars($player['team_name'] ?? 'N/A')); ?></p>
                    <p><strong>Age:</strong> <?php echo htmlspecialchars($player['age']); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($player['gender']); ?></p>
                </div>
            </div>
            <div class="id-card-footer">
                <p>Barangay Sports Registration System</p>
            </div>
        </div>
        <div class="text-center mt-4 no-print">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print ID Card
            </button>
            <a href="dashboard.php?page=all-players" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to All Players
            </a>
        </div>
    </div>
    <script src="../js/fontawesome.min.js"></script>
</body>
</html>
