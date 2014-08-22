window.onload = function() {
  ajaxJSON('/getJSONStats').then(function(r) {
    var chart1 = new Highcharts.Chart({
      chart: {
        renderTo: 'stats_active_time',
        type: 'column',
        zoomType: 'x'
      },
      title: {
        text: 'Hourly Activity'
      },
      legend: { enabled: false },
      xAxis: {
        categories: [
          '00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00',
          '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00',
          '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'
        ]
      },
      yAxis: {
        title: {
          text: 'Messages'
        }
      },
      series: [{
        name: 'Messages Sent',
        data: r.statistics.active_time
      }]
    });

    var letter_stats = [],
        categories = [];
    for(var i in r.statistics.letter_usage) {
      if(i.trim() != '') {
        categories.push(i);
        letter_stats.push(r.statistics.letter_usage[i]);
      }
    }
    var chart2 = new Highcharts.Chart({
      chart: {
        renderTo: 'stats_letter_usage',
        type: 'column',
        zoomType: 'x'
      },
      title: {
        text: 'Character Usage'
      },
      legend: { enabled: false },
      xAxis: { categories: categories },
      yAxis: { title: { text: 'Instances' } },
      series: [{ name: '', data: letter_stats}]
    });

    var all_users = [],
        num_top = 10,
        total_user_num = 0;
    
    for(var username in r.statistics.users) { total_user_num++; }
    for(var username in r.statistics.users) {
      if(username.indexOf("devict-bot") > -1) { continue; }
      for(var i=0; i < total_user_num; i++) {
        var do_break = false;
        if(typeof all_users[i] == 'undefined') {
          all_users[i] = { "name": username, "messages": r.statistics.users[username].message_count };
          do_break = true;
        } else {
          if(all_users[i].messages >= r.statistics.users[username].message_count) {
            continue;
          } else {
            // DETHRONED!
            all_users.splice(i, 0, {"name":username, "messages":r.statistics.users[username].message_count});
            do_break = true;
          }
        }
        if(do_break) { break; }
      }
    }
    // All Users List
    var B_utbl_body = B('#stats_user_list>div>table>tbody'),
        alt_row = 0;
    for(var i in all_users) {
      var B_row = B('<tr>');
      B_row.append(B('<td>').text(i+'.').addClass('users_rank_col'));
      B_row.append(B('<td>').text(all_users[i].name).addClass('users_name_col'));
      B_row.append(B('<td>').text(all_users[i].messages).addClass('users_messages_col'));
      if(alt_row++ % 2 == 0) {
        B_row.css('background-color','#AAA');
      }
      B_utbl_body.append(B_row);
    }

    // Top Users Chart
    top_users = all_users.slice(0, num_top);
    var categories = [],
        user_messages = [];
    for(var i in top_users) {
      categories.push(top_users[i].name);
      user_messages.push(top_users[i].messages);
    }
    var chart3 = new Highcharts.Chart({
      chart: {
        renderTo: 'stats_top_users',
        type: 'bar'
      },
      title: {
        text: 'User Participation'
      },
      legend: { enabled: false },
      xAxis: { categories: categories },
      yAxis: { title: { text: 'Messages' } },
      series: [{ name: 'Messages', data: user_messages }]
    });
  });
};
