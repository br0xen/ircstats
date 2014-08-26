<table class="pure-table pure-table-bordered">
  <thead>
    <tr>
      <th>Date</th>
      <th>From User</th>
      <th>Links</th>
      <th>View Message</th>
    </tr>
  </thead>
  <tbody>
<?php foreach($link_messages as $a_link): ?>
<?php if(strpos($a_link['nick'], 'devict-bot') !== false) { continue; } ?>
<?php   $link_text = ''; ?>
<?php   $link = extract_links($a_link['message']); ?>
<?php   foreach(array_shift($link) as $p_link): ?>
<?php     $link_text.=$p_link.'<br />'; ?>
<?php   endforeach; ?>
    <tr>
      <td><?php echo date('Y-m-d H:i', $a_link['time']); ?>
      <td><?php echo $a_link['nick'];?></td>
      <td><?php echo linkify($link_text, "_blank"); ?></td>
      <td><a class="view_message_link" data-open_message_idx="<?php echo $a_link['id'];?>">[Message]</a></td>
    </tr>
    <tr class="message hide" data-is_open="false" data-message_idx="<?php echo $a_link['id'];?>">
      <td colspan="4"><?php echo $a_link['message']; ?>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
