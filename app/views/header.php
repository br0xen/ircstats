<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $page_title; ?></title>
    <?php foreach($stylesheets as $a_sheet): ?>
    <link rel="stylesheet" href="<?php echo $a_sheet; ?>" type="text/css" media="all" />
    <?php endforeach; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body>
