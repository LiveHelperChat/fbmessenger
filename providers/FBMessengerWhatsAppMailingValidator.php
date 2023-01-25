<?php

namespace LiveHelperChatExtension\fbmessenger\providers;

class FBMessengerWhatsAppMailingValidator {

    public static function limitContactList() {

        $listParams = array(
            'sort' => 'name ASC, id ASC',
            'limit' => false);

        if (!\erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsappmessaging','all_contact_list')) {
            $listParams['customfilter'][] = ' (private = 0 OR user_id = ' . (int)\erLhcoreClassUser::instance()->getUserID() . ')';
        }

        return $listParams;
    }

    public static function pauseCampaign($item) {

        // Pause campaign action set's campaign status to pending.
        $item->status = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::STATUS_PENDING;
        $item->enabled = 0;
        $item->updateThis(['update' => ['status','enabled']]);

        $db = \ezcDbInstance::get();

        // Reverse all campaign recipients on pause
        $stmt = $db->prepare( 'UPDATE `lhc_fbmessengerwhatsapp_campaign_recipient` SET status = :status, `message_id` = 0 WHERE `id` IN (SELECT `campaign_recipient_id` FROM `lhc_fbmessengerwhatsapp_message` WHERE `status` = :status_message AND `campaign_id` = :campaign_id)');
        $stmt->bindValue(':campaign_id',$item->id,\PDO::PARAM_INT);
        $stmt->bindValue(':status_message', \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_SCHEDULED,\PDO::PARAM_INT);
        $stmt->bindValue(':status', \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_PENDING,\PDO::PARAM_INT);
        $stmt->execute();

        // Delete message until it was send
        $stmt = $db->prepare( 'DELETE FROM `lhc_fbmessengerwhatsapp_message` WHERE `status` = :status_message AND `campaign_id` = :campaign_id');
        $stmt->bindValue(':campaign_id',$item->id,\PDO::PARAM_INT);
        $stmt->bindValue(':status_message', \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_SCHEDULED,\PDO::PARAM_INT);
        $stmt->execute();
    }


    public static function exportMessagesCSV($filter) {
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename=fb-messages-report.csv");
        header("Content-Transfer-Encoding: binary");

        $df = fopen("php://output", 'w');

        $firstRow = [
            'id',
            'user_id',
            'user',
            'created_at',
            'updated_at',
            'phone',
            'phone_whatsapp',
            'phone_sender',
            'phone_sender_id',
            'status',
            'template',
            'template_id',
            'message',
            'language',
            'fb_msg_id',
            'send_status_raw',
            'conversation_id',
            'dep_id',
            'dep',
            'chat_id',
            'initiation',
            'message_variables',
            'business_account_id',
            'business_account',
            'campaign_id',
            'campaign',
            'campaign_recipient_id',
            'recipient_id',
            'private',
        ];

        fputcsv($df, $firstRow);

        $chunks = ceil(\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::getCount($filter)/300);

        $status = [
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_PENDING => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Pending'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_SENT => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Send'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_IN_PROCESS => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','In progress'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_FAILED => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Failed'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_REJECTED => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Rejected'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_READ => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Read'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_SCHEDULED => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Scheduled'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_DELIVERED => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Delivered'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_PENDING_PROCESS => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Pending process'),
        ];

        for($i = 0; $i < $chunks; $i ++) {
            $filterChunk = $filter;
            $filterChunk['offset'] = $i * 300;
            $filterChunk['limit'] = 300;

            foreach (\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::getList($filterChunk) as $item) {
                $itemCSV = [];

                $itemCSV[] = (string)$item->id;
                $itemCSV[] = (string)$item->user_id;
                $itemCSV[] = (string)$item->user;
                $itemCSV[] = $item->created_at > 0 ? date(\erLhcoreClassModule::$dateFormat, $item->created_at) : 'n/a';
                $itemCSV[] = $item->updated_at > 0 ? date(\erLhcoreClassModule::$dateFormat, $item->updated_at) : 'n/a';
                $itemCSV[] = (string)$item->phone;
                $itemCSV[] = (string)$item->phone_whatsapp;
                $itemCSV[] = (string)$item->phone_sender;
                $itemCSV[] = (string)$item->phone_sender_id;
                $itemCSV[] = $status[$item->status];
                $itemCSV[] = (string)$item->template;
                $itemCSV[] = (string)$item->template_id;
                $itemCSV[] = (string)$item->message;
                $itemCSV[] = (string)$item->language;
                $itemCSV[] = (string)$item->fb_msg_id;
                $itemCSV[] = (string)$item->send_status_raw;
                $itemCSV[] = (string)$item->conversation_id;
                $itemCSV[] = (string)$item->dep_id;
                $itemCSV[] = (string)$item->department;
                $itemCSV[] = (string)$item->chat_id;
                $itemCSV[] = (string)$item->initiation;
                $itemCSV[] = (string)$item->message_variables;
                $itemCSV[] = (string)$item->business_account_id;
                $itemCSV[] = (string)$item->business_account;
                $itemCSV[] = (string)$item->campaign_id;
                $itemCSV[] = (string)$item->campaign;
                $itemCSV[] = (string)$item->campaign_recipient_id;
                $itemCSV[] = (string)$item->recipient_id;
                $itemCSV[] = (string)$item->private;
                fputcsv($df, $itemCSV);
            }
        }

        fclose($df);
    }

    public static function exportCampaignRecipientCSV($filter, $params) {
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename=fb-campaign-recipient-" . $params['campaign']->id . ".csv");
        header("Content-Transfer-Encoding: binary");

        $df = fopen("php://output", 'w');

        $firstRow = [
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
            'file_4',



            'status',
            'send_at',
            'opened_at',
            'message_id',
            'conversation_id',
            'type',
            'log',
        ];

        fputcsv($df, $firstRow);

        $chunks = ceil(\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::getCount($filter)/300);

        $status = [
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_PENDING => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Pending'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_SENT => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Send'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_IN_PROCESS => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','In progress'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_FAILED => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Failed'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_REJECTED => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Rejected'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_READ => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Read'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_SCHEDULED => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Scheduled'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_DELIVERED => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Delivered'),
            \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_PENDING_PROCESS => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Pending process'),
        ];

        for($i = 0; $i < $chunks; $i ++) {
            $filterChunk = $filter;
            $filterChunk['offset'] = $i * 300;
            $filterChunk['limit'] = 300;

            foreach (\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::getList($filterChunk) as $item) {
                $itemCSV = [];
                $itemCSV[] = (string)$item->recipient_phone;
                $itemCSV[] = (string)$item->recipient_phone_recipient;
                $itemCSV[] = (string)$item->recipient;

                $itemCSV[] = (string)$item->name_front;
                $itemCSV[] = (string)$item->title_front;
                $itemCSV[] = (string)$item->lastname_front;
                $itemCSV[] = (string)$item->company_front;
                $itemCSV[] = $item->date > 0 ? (string)date('Y-m-d\TH:i',$item->date) : '';

                $itemCSV[] = (string)$item->attr_str_1_front;
                $itemCSV[] = (string)$item->attr_str_2_front;
                $itemCSV[] = (string)$item->attr_str_3_front;
                $itemCSV[] = (string)$item->attr_str_4_front;
                $itemCSV[] = (string)$item->attr_str_5_front;
                $itemCSV[] = (string)$item->attr_str_6_front;

                $itemCSV[] = (string)$item->file_1_url;
                $itemCSV[] = (string)$item->file_2_url;
                $itemCSV[] = (string)$item->file_3_url;
                $itemCSV[] = (string)$item->file_4_url;

                $itemCSV[] = $status[$item->status];
                $itemCSV[] = $item->send_at > 0 ? date(\erLhcoreClassModule::$dateFormat, $item->send_at) : 'n/a';
                $itemCSV[] = $item->opened_at > 0 ? date(\erLhcoreClassModule::$dateFormat, $item->opened_at) : 'n/a';
                $itemCSV[] = (string)$item->message_id;
                $itemCSV[] = (string)$item->conversation_id;
                $itemCSV[] = (string)$item->type == 1 ? 'manual' : 'list';
                $itemCSV[] = (string)$item->log;
                fputcsv($df, $itemCSV);
            }
        }

        fclose($df);
    }

    public static function validateMailingList($item) {
        $definition = array(
            'name' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'private' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
        );

        $form = new \ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ($form->hasValidData( 'name' )) {
            $item->name = $form->name;
        } else {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter a name!');
        }

        if ($form->hasValidData( 'private' ) && $form->private == true) {
            $item->private = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContactList::LIST_PRIVATE;
        } else {
            $item->private = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContactList::LIST_PUBLIC;
        }
        
        return $Errors;
    }

    public static function validateCampaign($item) {

        $definition = array(
            'name' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'starts_at' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'enabled' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'private' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'activate_again' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'dep_id' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
            ),
            'business_account_id' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'template' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'phone_sender_id' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_1' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_2' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_3' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_4' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_5' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_6' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_header_1' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_header_2' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_header_3' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_header_4' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_header_5' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_header_6' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_header_doc_1' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_header_doc_filename_1' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_header_img_1' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'field_header_video_1' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
        );

        $form = new \ezcInputForm( \INPUT_POST, $definition );
        $Errors = array();

        if ($form->hasValidData( 'dep_id' )) {
            $item->dep_id = $form->dep_id;
        } else {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please choose a department!');
        }

        if ($form->hasValidData( 'name' )) {
            $item->name = $form->name;
        } else {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter a name!');
        }

        if ($form->hasValidData( 'starts_at' )) {
            $item->starts_at = \strtotime($form->starts_at);
        } else {
            $item->starts_at = 0;
        }

        if ($form->hasValidData( 'private' ) && $form->private == true) {
            $item->private = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::LIST_PRIVATE;
        } else {
            $item->private = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::LIST_PUBLIC;
        }

        if ($form->hasValidData( 'business_account_id' )) {
            $item->business_account_id = \strtotime($form->business_account_id);
        } else {
            $item->business_account_id = 0;
        }

        if ($form->hasValidData( 'enabled' ) && $form->enabled == true) {
            $item->enabled = 1;
        } else {
            $item->enabled = 0;
        }

        if ($form->hasValidData( 'activate_again' ) && $form->activate_again == true) {
            $item->status = \LiveHelperChatExtension\fbmessenger\provider\erLhcoreClassModelMessageFBWhatsAppCampaign::STATUS_PENDING;
        }

        if ($form->hasValidData( 'phone_sender_id' )) {
            $item->phone_sender_id = $form->phone_sender_id;
        } else {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please choose a send phone!');
        }

        if ($form->hasValidData( 'template' ) && $form->template != '') {
            $template = explode('||',$form->template);
            $item->template = $template[0];
            $item->language = $template[1];
        } else {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please choose a template!');
        }

        $messageVariables = [];

        for ($i = 0; $i < 6; $i++) {
            if ($form->hasValidData('field_' . $i) && $form->{'field_' . $i}) {
                $messageVariables['field_' . $i] = $form->{'field_' . $i};
            }
        }

        for ($i = 0; $i < 6; $i++) {
            if ($form->hasValidData('field_header_' . $i) && $form->{'field_header_' . $i}) {
                $messageVariables['field_header_' . $i] = $form->{'field_header_' . $i};
            }
        }

        for ($i = 0; $i < 6; $i++) {
            if ($form->hasValidData('field_header_img_' . $i) && $form->{'field_header_img_' . $i}) {
                $messageVariables['field_header_img_' . $i] = $form->{'field_header_img_' . $i};
            }
        }

        for ($i = 0; $i < 6; $i++) {
            if ($form->hasValidData('field_header_video_' . $i) && $form->{'field_header_video_' . $i}) {
                $messageVariables['field_header_video_' . $i] = $form->{'field_header_video_' . $i};
            }
        }

        for ($i = 0; $i < 6; $i++) {
            if ($form->hasValidData('field_header_doc_' . $i) && $form->{'field_header_doc_' . $i}) {
                $messageVariables['field_header_doc_' . $i] = $form->{'field_header_doc_' . $i};
                $messageVariables['field_header_doc_filename_' . $i] = $form->hasValidData('field_header_doc_filename_' . $i) && $form->{'field_header_doc_filename_' . $i} ? $form->{'field_header_doc_filename_' . $i} : '';
            }
        }

        $item->message_variables_array = $messageVariables;
        $item->message_variables = json_encode($messageVariables);

        return $Errors;
    }

    public static function validateCampaignRecipient($item) {
        $definition = array(
            'email' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
            ),
            'name' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'phone' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'phone_recipient' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_1' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_2' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_3' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_4' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_5' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_6' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),

            'date' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'title' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'lastname' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'company' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'file_1' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'file_2' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'file_3' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'file_4' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),

        );

        $form = new \ezcInputForm( \INPUT_POST, $definition );
        $Errors = array();

        if ($form->hasValidData( 'email' )) {
            $item->email = $form->email;
        } else {
            $item->email = '';
        }

        if ($form->hasValidData( 'name' )) {
            $item->name = $form->name;
        }

        if ($form->hasValidData( 'phone' )) {
            $item->phone = trim(str_replace('+','',$form->phone));
        } else {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter a phone!');
        }

        if ($form->hasValidData( 'phone_recipient' )) {
            $item->phone_recipient = trim(str_replace('+','',$form->phone_recipient));
        }

        if ($form->hasValidData( 'attr_str_1' )) {
            $item->attr_str_1 = $form->attr_str_1;
        }

        if ($form->hasValidData( 'attr_str_2' )) {
            $item->attr_str_2 = $form->attr_str_2;
        }

        if ($form->hasValidData( 'attr_str_3' )) {
            $item->attr_str_3 = $form->attr_str_3;
        }

        if ($form->hasValidData( 'attr_str_4' )) {
            $item->attr_str_4 = $form->attr_str_4;
        }

        if ($form->hasValidData( 'attr_str_5' )) {
            $item->attr_str_5 = $form->attr_str_5;
        }

        if ($form->hasValidData( 'attr_str_6' )) {
            $item->attr_str_6 = $form->attr_str_6;
        }

        if ($form->hasValidData( 'date' )) {
            $item->date = \strtotime($form->date);
        } else {
            $item->date = 0;
        }

        if ($form->hasValidData( 'title' )) {
            $item->title = $form->title;
        } else {
            $item->title = '';
        }

        if ($form->hasValidData( 'lastname' )) {
            $item->lastname = $form->lastname;
        } else {
            $item->lastname = '';
        }

        if ($form->hasValidData( 'company' )) {
            $item->company = $form->company;
        } else {
            $item->company = '';
        }

        if ($form->hasValidData( 'file_1' )) {
            $item->file_1 = $form->file_1;
        } else {
            $item->file_1 = '';
        }

        if ($form->hasValidData( 'file_2' )) {
            $item->file_2 = $form->file_2;
        } else {
            $item->file_2 = '';
        }

        if ($form->hasValidData( 'file_3' )) {
            $item->file_3 = $form->file_3;
        } else {
            $item->file_3 = '';
        }

        if ($form->hasValidData( 'file_4' )) {
            $item->file_4 = $form->file_4;
        } else {
            $item->file_4 = '';
        }

        if ($item->id == null && \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::getCount(['filter' => ['campaign_id' => $item->campaign_id, 'phone' => $item->phone]]) == 1) {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','This recipient already exists in this campaign!');
        }

        return $Errors;
    }

    public static function validateMailingRecipient($item) {
        $definition = array(
            'email' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
            ),
            'disabled' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'ml' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1),FILTER_REQUIRE_ARRAY
            ),
            'name' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'phone' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'phone_recipient' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_1' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_2' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_3' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_4' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_5' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_6' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),

            'title' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'lastname' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'company' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'date' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'delivery_status' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0),
            ),
            'file_1' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'file_2' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'file_3' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'file_4' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
        );
        
        $form = new \ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ($form->hasValidData( 'date' )) {
            $item->date = \strtotime($form->date);
        } else {
            $item->date = 0;
        }

        if ($form->hasValidData( 'delivery_status' )) {
            $item->delivery_status = $form->delivery_status;
        } else {
            $item->delivery_status = 0;
        }

        if ($form->hasValidData( 'title' )) {
            $item->title = $form->title;
        } else {
            $item->title = '';
        }

        if ($form->hasValidData( 'lastname' )) {
            $item->lastname = $form->lastname;
        } else {
            $item->lastname = '';
        }

        if ($form->hasValidData( 'company' )) {
            $item->company = $form->company;
        } else {
            $item->company = '';
        }

        if ($form->hasValidData( 'file_1' )) {
            $item->file_1 = $form->file_1;
        } else {
            $item->file_1 = '';
        }

        if ($form->hasValidData( 'file_2' )) {
            $item->file_2 = $form->file_2;
        } else {
            $item->file_2 = '';
        }

        if ($form->hasValidData( 'file_3' )) {
            $item->file_3 = $form->file_3;
        } else {
            $item->file_3 = '';
        }

        if ($form->hasValidData( 'file_4' )) {
            $item->file_4 = $form->file_4;
        } else {
            $item->file_4 = '';
        }

        if ($form->hasValidData( 'email' )) {
            $item->email = $form->email;
        } else {
            $item->email = '';
        }

        if ($form->hasValidData( 'name' )) {
            $item->name = $form->name;
        }

        if ($form->hasValidData( 'phone' ) && $form->phone != '') {
            $item->phone = trim(str_replace('+','',$form->phone));
        } else {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter a phone');
        }

        if ($form->hasValidData( 'phone_recipient' ) && $form->phone_recipient != '') {
            $item->phone_recipient = trim(str_replace('+','',$form->phone_recipient));
        }

        if ($form->hasValidData( 'attr_str_1' )) {
            $item->attr_str_1 = $form->attr_str_1;
        }

        if ($form->hasValidData( 'attr_str_2' )) {
            $item->attr_str_2 = $form->attr_str_2;
        }

        if ($form->hasValidData( 'attr_str_3' )) {
            $item->attr_str_3 = $form->attr_str_3;
        }

        if ($form->hasValidData( 'attr_str_4' )) {
            $item->attr_str_4 = $form->attr_str_4;
        }

        if ($form->hasValidData( 'attr_str_5' )) {
            $item->attr_str_5 = $form->attr_str_5;
        }

        if ($form->hasValidData( 'attr_str_6' )) {
            $item->attr_str_6 = $form->attr_str_6;
        }

        if ($form->hasValidData( 'ml' ) && !empty($form->ml)) {
            $item->ml_ids = $item->ml_ids_front = $form->ml;
        } else {
            $item->ml_ids = [];
        }

        if ($form->hasValidData( 'disabled' ) && $form->disabled == true) {
            $item->disabled = 1;
        } else {
            $item->disabled = 0;
        }

        if ($item->id == null && \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::getCount(['filter' => ['phone' => $item->phone]]) == 1) {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','This contact already exists, edit contact and assign it to this list!');
        }

        return $Errors;
    }

    public static function getTemplates()
    {

        $db = \ezcDbInstance::get();

        // Reverse all campaign recipients on pause
        $stmt = $db->prepare( "SELECT `template` FROM `lhc_fbmessengerwhatsapp_message` GROUP BY `template`");
        $stmt->execute();
        $templates = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $items = [];
        foreach ($templates as $template) {
            $item = new \stdClass();
            $item->name = $item->id = $template;
            $items[] = $item;
        }

        return $items;
    }

    public static function getStatus()
    {
        $items = [
            ['id' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_PENDING, 'name' => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Pending')],
            ['id' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_SENT, 'name' => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Sent')],
            ['id' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_IN_PROCESS, 'name' => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','In progress')],
            ['id' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_FAILED, 'name' => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Failed')],
            ['id' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_REJECTED, 'name' => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Rejected')],
            ['id' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_READ, 'name' => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Has been read')],
            ['id' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_SCHEDULED, 'name' => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Scheduled')],
            ['id' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_DELIVERED, 'name' => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Delivered')],
            ['id' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_PENDING_PROCESS, 'name' => \erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Pending process')],
        ];

        return json_decode(json_encode($items));
    }
}

?>