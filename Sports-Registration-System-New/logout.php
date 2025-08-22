<?php
require_once 'includes/session.php';
require_once 'includes/functions.php';

session_destroy();
redirect('login.php');
?>
