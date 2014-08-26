window.onload = function() {
  B('.view_message_link').on('click',function(){
    var B_t = B(this),
        ex_idx = B_t.attr('data-open_message_idx'),
        msg_row = B('[data-message_idx="'+ex_idx+'"]');
    if(msg_row.attr('data-is_open') == 'true') {
      msg_row.addClass('hide');
      msg_row.attr('data-is_open',false);
    } else {
      msg_row.removeClass('hide');
      msg_row.attr('data-is_open',true);
    }
  });
};
