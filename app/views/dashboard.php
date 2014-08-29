<div class="content pure-g">
  <div id="stats_user_list" class="pure-u-1-5">
    <h3>Users by Activity</h3>
    <div>
      <table>
        <thead><tr><th>Rank</th><th>Username</th><th>Messages</th></tr></thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
  <a name="top_users"></a>
  <div id="stats_top_users" class="pure-u-3-5"></div>
  <a name="active_time"></a>
  <div id="stats_active_time" class="pure-u-4-5"></div>
  <a name="letter_usage"></a>
  <div id="stats_letter_usage" class="pure-u-4-5"></div>
  <a name="word_usage"></a>
  <div id="stats_word_usage_wrapper" class="pure-u-4-5 pure-form">
    <div id="stats_word_usage"></div>
    <label for="min_word_length">Minimum Word Length:</label>
    <input class="pure-u-1-24" type="text" name="min_word_length" id="min_word_length" />
    <button id="update_min_word_length" class="pure-button" type="button">Update</button>
    <button id="reset_min_word_length" class="pure-button" type="button">Reset</button>
  </div>
</div>
<script>
  <?php if(isset($user) && !empty($user)): ?>
  var user = "<?php echo urldecode($user); ?>";
  <?php else: ?>
  var user = "";
  <?php endif; ?>
  <?php if(isset($override_min_word_length)): ?>
  var override_min_word_length = <?php echo $override_min_word_length; ?>;
  <?php endif; ?>
</script>
