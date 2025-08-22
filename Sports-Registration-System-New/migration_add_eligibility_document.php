<?php
require_once 'includes/db_con.php';

$sql = "ALTER TABLE `players` ADD `eligibility_document` VARCHAR(255) NOT NULL AFTER `photo`";

if ($db_con->query($sql) === TRUE) {
    echo "Table 'players' altered successfully";
} else {
    echo "Error altering table: " . $db_con->error;
}

$db_con->close();
?>
