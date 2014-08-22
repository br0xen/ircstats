var selected_ids = [];

window.onload = function() {
  B('div.irc_message,div.irc_action').on('click',function(){
    var B_t = B(this);
    if((rem_idx = selected_ids.indexOf(B_t.attr('data-id'))) > -1) {
      B_t.removeClass('selected');
      selected_ids.splice(rem_idx, 1);
    } else {
      B_t.addClass('selected');
      selected_ids.push(B_t.attr('data-id'));
    }
    // Now check if we need to display any addtional popup buttons
    if(selected_ids.length > 0) {
      B('#view_selected').css('display','inline');
      if(selected_ids.length > 1) {
        B('#view_range').css('display','inline');
      } else {
        B('#view_range').css('display','none');
      }
    } else {
      B('#view_selected,#view_range').css('display','none');
    }
  });
  B('#view_selected').on('click',function(){
    var sel_ids = '';
    if(selected_ids.length > 0) {
      for(var i in selected_ids) {
        sel_ids+=selected_ids[i]+',';
      }
      window.location='/history/ids/'+sel_ids;
    } else {
      setTooltip('No activity selected!', 1);
    }
  });
  B('#view_range').on('click',function(){
    var start_id = Infinity,
        end_id = -1;
    for(var i in selected_ids) {
      start_id = (selected_ids[i] < start_id)?selected_ids[i]:start_id;
      end_id = (selected_ids[i] > end_id)?selected_ids[i]:end_id;
    }
    if(start_id < Infinity && end_id > -1) {
      window.location='/history/from_id/'+start_id+'/to_id/'+end_id;
    }
  });
  B('#jump_to_top').on('click',function(){
    pageScroll(-1, -10);
  });
  B('#jump_to_bottom').on('click',function(){
    pageScroll(1, 10);
  });
  B('#view_previous_day').on('click',function(){
    window.location='/history/start/'+jump_back_time;
  });
  B('#clear_search').on('click',function(){
    window.location='/history';
  });
  B('#search_field').on('keyup', function(e) {
    if(e.keyCode == 13) {
      var srch_val = B('#search_field').val();
      window.location='/history/search/'+srch_val;
    }
  });
  B('#search').on('click',function(){
    if(B('#search').attr('data-open')) {
      B('#search_field').css('display','none');
      B('#search').attr('data-open','');
    } else {
      B('#search_field').css('display','inline');
      B('#search').attr('data-open',true);
      B('#search_field')[0].focus();
    }
  });
  B('.display_tooltip').on('mousemove',function(){
    var tooltip_text = B(this).attr('data-tooltip');
    setTooltip(tooltip_text);
  });
};

function setTooltip(txt, priority) {
  priority = (typeof priority == 'undefined')?'0':priority;
  var curr_priority = B('.popup_tooltip').attr('data-priority');
  if(curr_priority <= priority) {
    var tt_id = B('.popup_tooltip').attr('data-timeout_id');
    B('.popup_tooltip').text(txt).css('display','inline');
    if(tt_id != -1) {
      clearTimeout(tt_id);
    }
    tt_id = setTimeout(function(){
      B('.popup_tooltip').text('').css('display','none').attr('data-timeout_id',-1);
    }, 5000);
    B('.popup_tooltip').attr('data-timeout_id',tt_id);
  }
}

function pageScroll(upordown, accel) {
  if(typeof accel == 'undefined') { accel = 0; }
  switch(upordown) {
    case 'up':
      upordown = -1;
      break;
    case 'down':
      upordown = 1;
      break;
  }
  var doc = document.documentElement,
      oldtop = (window.pageYOffset || doc.scrollTop)  - (doc.clientTop || 0);
  window.scrollBy(0, upordown);
  var newtop = (window.pageYOffset || doc.scrollTop)  - (doc.clientTop || 0);
  if(oldtop != newtop) {
    scrolldelay = setTimeout(function(){pageScroll((upordown + accel), accel);},10);
  }
}
