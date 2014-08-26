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
    <div class="header">
      <div class="pure-menu pure-menu-open pure-menu-horizontal">
        <a href=""><?php echo $current_channel.' - '.$view_title;?></a>
        <ul>
          <li class="<?php echo (!empty($current_page) && $current_page=='statistics')?'pure-menu-selected':''?>"><a href="/statistics">Statistics</a></li>
          <li class="<?php echo (!empty($current_page) && $current_page=='history')?'pure-menu-selected':''?>"><a href="/history">History</a></li>
          <li class="<?php echo (!empty($current_page) && $current_page=='links')?'pure-menu-selected':''?>"><a href="/links">Links</a></li>
        </ul>
      </div>
    </div>
