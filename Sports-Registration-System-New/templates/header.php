<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <?php
    $css_path = 'css/';
    if (!is_dir($css_path)) {
        $css_path = '../css/';
    }
    ?>
    <link rel="stylesheet" href="<?php echo $css_path; ?>bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $css_path; ?>style.css">
    <link rel="stylesheet" href="<?php echo $css_path; ?>dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo $css_path; ?>fontawesome.min.css">
    <link rel="stylesheet" href="<?php echo $css_path; ?>solid.min.css">
    <?php if (basename($_SERVER['PHP_SELF']) == 'register.php') { ?>
        <link rel="stylesheet" href="<?php echo $css_path; ?>register.css">
    <?php } ?>

    <title><?php echo $page_title; ?></title>
</head>
<body>
