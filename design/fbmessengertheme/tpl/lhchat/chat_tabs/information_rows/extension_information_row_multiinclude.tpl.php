<?php
// Override me
//  - holds identifier for a row
?>

<?php if ($buttonData['item'] == 'fb_chat') : ?>

    <?php if (isset($chat->chat_variables_array['fb_chat']) && $chat->chat_variables_array['fb_chat'] == true) : ?>
    <tr>
        <td>
            <img width="14" src="<?php echo erLhcoreClassDesign::design('images/F_icon.svg')?>" title="Facebook chat" />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','FB Chat')?>
        </td>
        <td>
            <b>YES,</b> <?php echo htmlspecialchars($chat->chat_variables_array['fb_gender'])?>
        </td>
    </tr>
    <?php endif; ?>

<?php endif; ?>
