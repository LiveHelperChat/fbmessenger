<?php
#[\AllowDynamicProperties]
class erLhcoreClassFBValidator
{
    public static function validatePage(erLhcoreClassModelFBPage & $item)
    {
            $definition = array(
                'name' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                ),
                'page_token' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                ),
                'verify_token' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                ),
                'app_secret' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                ),
                'verified' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
                ),
                'bot_disabled' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
                ),
                'dep_id' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
                )
            );

            $form = new ezcInputForm( INPUT_POST, $definition );
            $Errors = array();
            
            if ( $form->hasValidData( 'name' ) && $form->name != '')
            {
                $item->name = $form->name;
            } else {
                $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please enter page name!');
            }
            
            if ( $form->hasValidData( 'app_secret' ) && $form->app_secret != '')
            {
                $item->app_secret = $form->app_secret;
            } else {
                $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please enter secret key!');
            }

            if ( $form->hasValidData( 'page_token' ) && $form->page_token != '')
            {
                $item->page_token = $form->page_token;
            } else {
                $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please enter page token!');
            }

            if ( $form->hasValidData( 'verify_token' ) && $form->verify_token != '')
            {
                $item->verify_token = $form->verify_token;
            } else {
                $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please enter verify token!');
            }

            if ( $form->hasValidData( 'dep_id' ))
            {
                $item->dep_id = $form->dep_id;
            } else {
                $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please choose a department!');
            }

            if ( $form->hasValidData( 'verified' ) && $form->verified == true)
            {
                $item->verified = 1;
            } else {
                $item->verified = 0;
            }

            if ( $form->hasValidData( 'bot_disabled' ) && $form->bot_disabled == true)
            {
                $item->bot_disabled = 1;
            } else {
                $item->bot_disabled = 0;
            }

            return $Errors;        
    }
    public static function validateNotification(erLhcoreClassModelFBNotificationSchedule & $item)
    {
        $definition = array(
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'start_at' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'start_at_hours' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'start_at_minutes' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),


            // Buttons options
            'interval' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'dep_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
            ),
            'amount' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'filter_gender' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'message' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'processed' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( $form->hasValidData( 'name' ) && $form->name != '') {
            $item->name = $form->name;
        } else {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please enter name!');
        }

        if ( $form->hasValidData( 'processed' ) && $form->processed == true) {
            $item->status = erLhcoreClassModelFBNotificationSchedule::STATUS_PROCESSED;
        } else {
            $item->status = erLhcoreClassModelFBNotificationSchedule::STATUS_PENDING;
        }

        if ( $form->hasValidData( 'start_at' ) && $form->start_at != '') {

            $dateFormated = erLhcoreClassSearchHandler::formatDateToTimestamp($form->start_at);

            if ($dateFormated != false) {
                $item->start_at = $dateFormated;
                $item->start_at_day = $form->start_at;
            } else {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please enter start date!');
            }
        } else {
            $item->start_at = 0;
        }

        $appendHours = 0;
        if ( $form->hasValidData( 'start_at_hours' ) && $item->start_at > 0) {
            $appendHours = 3600 * $form->start_at_hours;
            $item->start_at_hour = $form->start_at_hours;
        }

        $appendMinutes = 0;
        if ( $form->hasValidData( 'start_at_minutes' ) && $item->start_at > 0) {
            $appendMinutes = 60 * $form->start_at_minutes;
            $item->start_at_minute = $form->start_at_minutes;
        }

        if ($item->start_at > 0) {
            $item->start_at = $item->start_at + $appendHours + $appendMinutes;
        }

        if ( $form->hasValidData( 'interval' ) ) {
            $item->interval = $form->interval;
        } else {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please enter interval!');
        }

        if ( $form->hasValidData( 'amount' ) ) {
            $item->amount = $form->amount;
        } else {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please enter batch size!');
        }

        $filterArray = $item->filter_array;

        if ( $form->hasValidData( 'filter_gender' ) && $form->filter_gender != '') {
            $filterArray['gender'] = $form->filter_gender;
        } else {
            $filterArray['gender'] = '';
        }

        if ( $form->hasValidData( 'dep_id' )) {
            $filterArray['dep_id'] = $form->dep_id;
        } else {
            $filterArray['dep_id'] = '';
        }

        if ( $form->hasValidData( 'message' ) && $form->message != '' ) {
            $item->message = $form->message;
        } else {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please enter message!');
        }

        $filterArray = array_filter($filterArray);

        $item->filter = json_encode($filterArray);
        $item->filter_array = $filterArray;

        return $Errors;
    }

    public static function validateChannel(erLhcoreClassModelFBChannel & $item)
    {
        $definition = array(
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( $form->hasValidData( 'name' ) && $form->name != '') {
            $item->name = $form->name;
        } else {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please enter name!');
        }

        return $Errors;
    }

    public static function validateBBCode(erLhcoreClassModelFBBBCode & $item)
    {
        $definition = array(
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'bbcode' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),

            // Buttons options
            'web_button_message' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'bbcode_button_type' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'web_list_button_default_web_title' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'web_list_button_default_web_url' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
        );

        for ($i = 1; $i <= 4;$i++){
            $definition['web_list_title_' . $i] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
            $definition['web_list_sub_title_' . $i] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
            $definition['web_list_sub_img_' . $i] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
            $definition['web_list_button_web_title_' . $i] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
            $definition['web_list_button_web_url_' . $i] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
            $definition['web_list_def_url_' . $i] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
        }

        for ($i = 1; $i <= 3;$i++){
            $definition['web_button_web_url_' . $i] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
            $definition['web_button_web_title_' . $i] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
        }

        for ($i = 1; $i <= 10;$i++){
            $definition['web_gen_button_title_' . $i] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
            $definition['web_gen_button_subtitle_' . $i] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
            $definition['web_gen_button_img_' . $i] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
            $definition['web_gen_button_def_url_' . $i] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');

            for ($n = 1; $n <= 3;$n++){
                $definition['web_button_web_title_' . $i . '_' . $n] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
                $definition['web_button_web_url_' . $i . '_' . $n] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
            }
        }

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( $form->hasValidData( 'name' ) && $form->name != '') {
            $item->name = $form->name;
        } else {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please enter BBCode name!');
        }

        if ( $form->hasValidData( 'bbcode' ) && $form->bbcode != '') {
            $item->bbcode = $form->bbcode;
        } else {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please enter BBCode!');
        }

        $item->configuration_array = array();

        if ( $form->hasValidData( 'bbcode_button_type' )) {
            $item->configuration_array['bbcode_button_type'] = $form->bbcode_button_type;
        }

        if ( $form->hasValidData( 'web_button_message' )) {
            $item->configuration_array['web_button']['web_button_message'] = $form->web_button_message;
        }

        for ($i = 1; $i <= 3;$i++) {
            if ( $form->hasValidData( 'web_button_web_url_' . $i  )) {
                $item->configuration_array['web_button']['web_button_web_url_' . $i] = $form->{'web_button_web_url_' . $i};
            }

            if ( $form->hasValidData( 'web_button_web_title_' . $i  )) {
                $item->configuration_array['web_button']['web_button_web_title_' . $i] = $form->{'web_button_web_title_' . $i};
            }
        }

        for ($i = 1; $i <= 10;$i++) {
            if ( $form->hasValidData( 'web_gen_button_title_' . $i  )) {
                $item->configuration_array['web_button_gen']['web_gen_button_title_' . $i] = $form->{'web_gen_button_title_' . $i};
            }

            if ( $form->hasValidData( 'web_gen_button_subtitle_' . $i  )) {
                $item->configuration_array['web_button_gen']['web_gen_button_subtitle_' . $i] = $form->{'web_gen_button_subtitle_' . $i};
            }

            if ( $form->hasValidData( 'web_gen_button_img_' . $i  )) {
                $item->configuration_array['web_button_gen']['web_gen_button_img_' . $i] = $form->{'web_gen_button_img_' . $i};
            }

            if ( $form->hasValidData( 'web_gen_button_def_url_' . $i  )) {
                $item->configuration_array['web_button_gen']['web_gen_button_def_url_' . $i] = $form->{'web_gen_button_def_url_' . $i};
            }

            for ($n = 1; $n <= 3; $n++) {

                if ( $form->hasValidData( 'web_button_web_title_' . $i . '_' . $n  )) {
                    $item->configuration_array['web_button_gen']['web_button_web_title_' . $i . '_' . $n] = $form->{'web_button_web_title_' . $i . '_' . $n};
                }

                if ( $form->hasValidData( 'web_button_web_url_' . $i . '_' . $n  )) {
                    $item->configuration_array['web_button_gen']['web_button_web_url_' . $i . '_' . $n] = $form->{'web_button_web_url_' . $i . '_' . $n};
                }
            }
        }

        if ( $form->hasValidData( 'web_list_button_default_web_title' )) {
            $item->configuration_array['web_button_list']['web_list_button_default_web_title'] = $form->web_list_button_default_web_title;
        }

        if ( $form->hasValidData( 'web_list_button_default_web_url' )) {
            $item->configuration_array['web_button_list']['web_list_button_default_web_url'] = $form->web_list_button_default_web_url;
        }

        for ($i = 1; $i <= 4;$i++){

            if ( $form->hasValidData( 'web_list_title_' . $i )) {
                $item->configuration_array['web_button_list']['web_list_title_' . $i] = $form->{'web_list_title_' . $i};
            }

            if ( $form->hasValidData( 'web_list_sub_title_' . $i )) {
                $item->configuration_array['web_button_list']['web_list_sub_title_' . $i] = $form->{'web_list_sub_title_' . $i};
            }

            if ( $form->hasValidData( 'web_list_sub_img_' . $i )) {
                $item->configuration_array['web_button_list']['web_list_sub_img_' . $i] = $form->{'web_list_sub_img_' . $i};
            }

            if ( $form->hasValidData( 'web_list_button_web_title_' . $i )) {
                $item->configuration_array['web_button_list']['web_list_button_web_title_' . $i] = $form->{'web_list_button_web_title_' . $i};
            }

            if ( $form->hasValidData( 'web_list_button_web_url_' . $i )) {
                $item->configuration_array['web_button_list']['web_list_button_web_url_' . $i] = $form->{'web_list_button_web_url_' . $i};
            }

            if ( $form->hasValidData( 'web_list_def_url_' . $i )) {
                $item->configuration_array['web_button_list']['web_list_def_url_' . $i] = $form->{'web_list_def_url_' . $i};
            }
        }

        $item->configuration = json_encode($item->configuration_array);

        return $Errors;
    }

    public static function renderElementsByBlock($block) {
        $elements = erLhcoreClassModelFBBotElement::getList(array('filter' => array('block_id' => $block->id)));
        erLhcoreClassChat::prefillGetAttributes($elements, array('id','type'), array(), array('remove_all' => true,'do_not_clean' => true));
        return array_values($elements);
    }
}