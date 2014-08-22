<div class="header">
  <div class="pure-menu pure-menu-open pure-menu-horizontal">
    <a href=""><?php echo $current_channel.' - '.$view_title;?></a>
    <ul>
      <li class="pure-menu-selected"><a href="/statistics">Statistics</a></li>
      <li><a href="/history">History</a></li>
    </ul>
  </div>
</div>
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
  <div id="stats_top_users" class="pure-u-3-5"></div>
  <div id="stats_active_time" class="pure-u-4-5"></div>
  <div id="stats_letter_usage" class="pure-u-4-5"></div>
  <div id="stats_word_usage" class="pure-u-4-5 hide"></div>
</div>
