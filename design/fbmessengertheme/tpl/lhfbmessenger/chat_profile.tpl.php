<?php
/*
$chatVariables = $chat->chat_variables_array;

if (isset($chatVariables['fb_chat']) && $chatVariables['fb_chat'] == 1) :

    $fbChat = erLhcoreClassModelFBChat::findOne(array('filter' => array('chat_id' => $chat->id)));

    if ($fbChat instanceof erLhcoreClassModelFBChat) {
        $fbLead = erLhcoreClassModelFBLead::findOne(array('filter' => array('user_id' => $fbChat->user_id)));
        if ($fbLead instanceof erLhcoreClassModelFBLead) : ?>
        <div class="pull-left"><h4 style="margin:0;line-height:40px;"><img class="img-responsive pull-left" width="40" src="<?php echo $fbLead->profile_pic_front?>" />&nbsp;<b><?php echo htmlspecialchars($fbLead->first_name) ?> <?php echo htmlspecialchars($fbLead->last_name) ?></b></h4></div>
        <?php endif;
    }
endif;*/ ?>