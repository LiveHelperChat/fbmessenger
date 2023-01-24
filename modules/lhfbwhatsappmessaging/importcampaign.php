<?php

$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();

$campaign = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::fetch($Params['user_parameters']['id']);

if (!($campaign instanceof \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign)) {
    die('Invalid campaign!');
}

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsappmessaging/importcampaign.tpl.php');

$itemDefault = new \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient();

if (isset($_POST['remove_old']) && $_POST['remove_old'] == true) {
    $tpl->set('remove_old', true);
}

if (isset($_POST['UploadFileAction'])) {

    $errors = [];

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        $errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','Missing CSRF Token!!');
    }
    
    if (empty($errors) && erLhcoreClassSearchHandler::isFile('files',array('csv'))) {

        $dir = 'var/tmpfiles/';
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.temppath', array('dir' => & $dir));

        erLhcoreClassFileUpload::mkdirRecursive( $dir );

        $filename = erLhcoreClassSearchHandler::moveUploadedFile('files', $dir);

        $header = NULL;
        $data = array();

        $canned = [
            'phone',
            'phone_recipient',
            'email',
            'name',
            'title',
            'lastname',
            'company',
            'date',
            'attr_str_1',
            'attr_str_2',
            'attr_str_3',
            'attr_str_4',
            'attr_str_5',
            'attr_str_6',
            'file_1',
            'file_2',
            'file_3',
            'file_4'
        ];

        if (($handle = fopen($dir . $filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 10000, ",")) !== FALSE)
            {
                $row = array_splice($row,0, count($canned));
                if(!$header) {
                    $header = $row;
                } else {
                    if (count($header) != count($row)) {
                        if (count($row) > count($header)) {
                            $row = array_splice($row,0, count($canned));
                        } else {
                            $row = $row + array_fill(count($row),count($header) - count($row),'');
                        }
                    }
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        unlink($dir . $filename);

        $stats = array(
            'updated' => 0,
            'imported' => 0,
            'removed' => 0,
        );

        if ($canned === $header) {

            if (isset($_POST['remove_old']) && $_POST['remove_old'] == true) {
                $db = ezcDbInstance::get();
                $stmt = $db->prepare('DELETE FROM `lhc_fbmessengerwhatsapp_campaign_recipient` WHERE `campaign_id` = :campaign_id');
                $stmt->bindValue(':campaign_id', $campaign->id, PDO::PARAM_INT);
                $stmt->execute();
            }

            foreach ($data as $item) {

                $item['date'] = strtotime($item['date']);

                $cannedMessage = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::findOne(array('filter' => array('campaign_id' => $campaign->id, 'phone' => $item['phone'])));

                if (!($cannedMessage instanceof \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient)) {
                    $cannedMessage = new \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient();
                    $stats['imported']++;
                } else {
                    $stats['updated']++;
                }

                $cannedMessage->campaign_id = $campaign->id;
                $cannedMessage->type = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::TYPE_MANUAL;
                $cannedMessage->setState($item);
                $cannedMessage->saveThis();
            }

            $tpl->set('update', $stats);
        } else {
            $tpl->set('errors', [erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','Expected columns does not match!')]);
        }

    } elseif (!empty($errors)) {
        $tpl->set('errors', $errors);
    } else {
        $tpl->set('errors', [erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','Invalid file format')]);
    }
}

$tpl->set('item', $itemDefault);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'popup';

?>