<?php
$chatVariables = $chat->chat_variables_array;
if (is_object($chat->iwh) && in_array($chat->iwh->scope,['facebookmessengerappscope','facebookinstagramappscope']) && is_object($chat->incoming_chat)) :
$fbLead = erLhcoreClassModelFBLead::findOne(array('filter' => array('user_id' => isset($chat->chat_variables_array['fb_user_id']) ? $chat->chat_variables_array['fb_user_id'] : $chat->incoming_chat->chat_external_first)));
if ($fbLead instanceof erLhcoreClassModelFBLead) : ?>
<div class="pull-left"><h4 style="margin:0;line-height:40px;"><img class="img-responsive pull-left" width="40" src="<?php echo $fbLead->profile_pic_front?>" />&nbsp;<b><?php echo htmlspecialchars($fbLead->first_name) ?> <?php echo htmlspecialchars($fbLead->last_name) ?></b></h4></div>
<?php endif; endif; ?>