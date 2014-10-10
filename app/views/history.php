<div class="content">
    <script>
      var jump_back_time = <?php echo $jump_back_day; ?>,
          current_end_tm = <?php echo $current_end_day; ?>;
    </script>
    <div class="popup_controls">
      <span id="view_selected" class="hide popup_button display_tooltip" data-tooltip="Only show selected activity">
        <i class="fa fa-filter"></i>
      </span>
      <span id="view_range" class="hide popup_button display_tooltip" data-tooltip="Show all activity from first selected to last">
        <i class="fa fa-arrows-v"></i>
      </span>
      <?php if(isset($is_search) && $is_searching): ?>
      <span id="clear_search" class="popup_button display_tooltip" data-tooltip="Clear current search/filters">
        <i class="fa fa-search-minus"></i>
      </span>
      <?php endif; ?>
      <span id="search" class="popup_button display_tooltip" data-tooltip="Search for a specific Term">
        <input id="search_field" class="hide" /><i class="fa fa-search"></i>
      </span>
      <span id="view_previous_day" class="popup_button display_tooltip" data-tooltip="Show more history">
        <i class="fa fa-history"></i>
      </span>
      <span id="jump_to_top" class="popup_button display_tooltip" data-tooltip="Scroll to top">
        <i class="fa fa-arrow-circle-up"></i>
      </span>
      <span id="jump_to_bottom" class="popup_button display_tooltip" data-tooltip="Scroll to bottom">
        <i class="fa fa-arrow-circle-down"></i>
      </span>
    </div>
    <div class="popup_tooltip">
    </div>
    <div class="history">
<?php foreach($history as $a_h): ?>
  <?php if($a_h['type'] == 'MESSAGE'): ?>
      <div class="irc_message" data-id="<?php echo $a_h['id'];?>">
        <span class="irc_time"><?php echo getDateString($a_h['time']); ?></span>
        <span>&lt;<span class="irc_nick"><?php echo $a_h['nick']; ?></span>&gt; </span>
        <span class="irc_message"><?php echo linkify(sanitize(convert_ascii($a_h['message'])), "_blank"); ?></span>
      </div>
  <?php elseif($a_h['type'] == 'ACTION'): ?>
      <div class="irc_action" data-id="<?php echo $a_h['id'];?>">
        <span class="irc_time"><?php echo getDateString($a_h['time']); ?></span>
        <span>* <span class="irc_nick"><?php echo $a_h['nick']; ?></span></span>
        <span class="irc_message"><?php echo linkify(sanitize(convert_ascii($a_h['message'])), "_blank"); ?></span>
      </div>
  <?php elseif($a_h['type'] == 'KICK'): ?>
      <div class="irc_kick" data-id="<?php echo $a_h['id'];?>">
        <span class="irc_time"><?php echo getDateString($a_h['time']); ?></span>
        <span>-!- <span class="irc_nick"><?php echo $a_h['nick']; ?></span></span>
        <span class="irc_message"><?php echo linkify(sanitize(convert_ascii('was kicked from '.$a_h['channel'].' by '.$a_h['kicker'].' ['.$a_h['message'].']')), "_blank"); ?></span>
      </div>
  <?php elseif($a_h['type'] == 'PART'): ?>
      <div class="irc_join" data-id="<?php echo $a_h['id'];?>">
        <span class="irc_time"><?php echo getDateString($a_h['time']); ?></span>
        <span>-!- <span class="irc_nick"><?php echo $a_h['nick']; ?></span></span>
        <span class="irc_message"><?php echo linkify(sanitize(convert_ascii('has left '.$a_h['channel'].' ['.$a_h['message'].']')), "_blank"); ?></span>
      </div>
  <?php elseif($a_h['type'] == 'JOIN'): ?>
      <div class="irc_join" data-id="<?php echo $a_h['id'];?>">
        <span class="irc_time"><?php echo getDateString($a_h['time']); ?></span>
        <span>-!- <span class="irc_nick"><?php echo $a_h['nick']; ?></span></span>
        <span class="irc_message"><?php echo linkify(sanitize(convert_ascii('has joined '.$a_h['channel'])), "_blank"); ?></span>
      </div>
  <?php elseif($a_h['type'] == 'QUIT'): ?>
      <div class="irc_join" data-id="<?php echo $a_h['id'];?>">
        <span class="irc_time"><?php echo getDateString($a_h['time']); ?></span>
        <span>-!- <span class="irc_nick"><?php echo $a_h['nick']; ?></span></span>
        <span class="irc_message"><?php echo linkify(sanitize(convert_ascii('has quit ['.$a_h['message'].']')), "_blank"); ?></span>
      </div>
  <?php endif; ?>
<?php endforeach; ?>
    </div>
</div>
