<?php
require_once 'includes/db_con.php';

$sql = "CREATE TABLE `team_invitations` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `coach_id` INT(11) NOT NULL,
    `player_id` INT(11) NOT NULL,
    `team_id` INT(11) NOT NULL,
    `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($db_con->query($sql) === TRUE) {
    echo "Table 'team_invitations' created successfully";
} else {
    echo "Error creating table: " . $db_con->error;
}

$db_con->close();
?>
