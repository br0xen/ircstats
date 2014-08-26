window.onload = function() {
  ajaxJSON('/getJSONStats').then(function(r) {
    var active_time_data = r.statistics.active_time,
        letter_stats_data = r.statistics.letter_usage;
    if(user != "") {
      if(typeof r.statistics.users[user] != 'undefined') {
        active_time_data = r.statistics.users[user].active_time;
        letter_stats_data = r.statistics.users[user].letter_usage;
      }
    }
    var letter_stats = [],
        categories = [];
    for(var i in letter_stats_data) {
      if(i.trim() != '') {
        categories.push(i);
        letter_stats.push(letter_stats_data[i]);
      }
    }
    var char_usage_chart = new Highcharts.Chart({
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
          all_users[i] = { 
              "name": username, 
              "messages": r.statistics.users[username].message_count,
              "active_time": r.statistics.users[username].active_time
          };
          do_break = true;
        } else {
          if(all_users[i].messages >= r.statistics.users[username].message_count) {
            continue;
          } else {
            // DETHRONED!
            all_users.splice(i, 0, {
                "name":username, 
                "messages":r.statistics.users[username].message_count,
                "active_time":r.statistics.users[username].active_time
            });
            do_break = true;
          }
        }
        if(do_break) { break; }
      }
    }
    // All Users List
    var B_utbl_body = B('#stats_user_list>div>table>tbody'),
        alt_row = 0,
        selected_user_idx = -1;
    for(var i in all_users) {
      if(all_users[i].name == user) {
        selected_user_idx = i;
      }
      var B_row = B('<tr>'),
          B_user_link = B('<a>').attr('href', '/statistics/user/'+all_users[i].name).text(all_users[i].name),
          B_user_td = B('<td>');
      B_user_td.append(B_user_link);
      B_row.append(B('<td>').text(i+'.').addClass('users_rank_col'));
      B_row.append(B_user_td);
      B_row.append(B('<td>').text(all_users[i].messages).addClass('users_messages_col'));
      if(alt_row++ % 2 == 0) {
        B_row.css('background-color','#AAA');
      }
      B_utbl_body.append(B_row);
    }

    // Top Users Chart
    if(selected_user_idx > 10) {
      var num_before = 3,
          num_after = (all_users.length - parseInt(selected_user_idx) < 3)?(all_users.length - parseInt(selected_user_idx)):3;
      if(num_after < 3) {
        num_before += 3 - num_after;
      }
      top_users = all_users.slice((parseInt(selected_user_idx) - num_before), (parseInt(selected_user_idx) + num_after+1));
    } else {
      top_users = all_users.slice(0, num_top);
    }
    var categories = [],
        user_messages = [],
        user_activity_series = [];
    for(var i in top_users) {
      categories.push(top_users[i].name);
      user_messages.push(top_users[i].messages);
    }
    var top_users_chart = new Highcharts.Chart({
      chart: {
        renderTo: 'stats_top_users',
        type: 'bar'
      },
      plotOptions: {
        bar: {
          dataLabels: {
            enabled: true,
            color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
            style: {
              textShadow: '0 0 3px black, 0 0 3px black'
            }
          }
        }
      },
      title: {
        text: 'User Participation'
      },
      legend: { enabled: false },
      xAxis: { categories: categories },
      yAxis: { title: { text: 'Messages' } },
      series: [{ name: 'Messages', data: user_messages }]
    });
    var active_times_chart;
    if(user == "") {
      for(var i = 0; i < 4; i++) {
        user_activity_series.push({name: top_users[i].name, data: top_users[i].active_time});
      }
      for(var i in user_activity_series) {
        for(var j in user_activity_series[i].data) {
          active_time_data[j]-=user_activity_series[i].data[j];
        }
      }
      user_activity_series.push({
          name: "Everyone Else",
          data: active_time_data
      });
      active_times_chart = new Highcharts.Chart({
        chart: {
          renderTo: 'stats_active_time',
          type: 'column',
          zoomType: 'x'
        },
        title: { text: 'Hourly Activity' },
        legend: { enabled: false },
        plotOptions: {
          column: {
            stacking: 'normal',
          }
        },
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
        series: user_activity_series
      });
    } else {
      active_times_chart = new Highcharts.Chart({
        chart: {
          renderTo: 'stats_active_time',
          type: 'column',
          zoomType: 'x'
        },
        title: {
          text: 'Hourly Activity'
        },
        legend: { enabled: false },
        plotOptions: {
          column: {
            dataLabels: {
              enabled: true,
              color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
              style: {
                textShadow: '0 0 3px black, 0 0 3px black'
              }
            }
          }
        },
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
          data: active_time_data
        }]
      });
    }
  });
};
