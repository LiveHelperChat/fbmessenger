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
                'page_id' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
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

            if ( $form->hasValidData( 'page_id' ))
            {
                $item->page_id = $form->page_id;
            } else {
                $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please enter Page ID!');
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

    public static function sendNotification($params = array())
    {
        $lead = erLhcoreClassModelFBLead::fetch($params['item']->lead_id);

        if (is_object($lead->page)) {
            $messenger = Tgallice\FBMessenger\Messenger::create($lead->page->page_token);

            $messages = erLhcoreClassExtensionFbmessenger::parseMessageForFB(str_replace(array('{first_name}','{last_name}'), array($lead->first_name,$lead->last_name), $params['schedule']->message));

            foreach ($messages as $msg) {
                if ($msg !== null) {
                    try {
                        $response = $messenger->sendMessage($lead->user_id, $msg);
                    } catch (Exception $e) {
                        $lead->blocked = 1;
                        $lead->saveThis();
                        throw $e;
                    }
                }
            }
        } else {
            throw new Exception('Page could not be found!');
        }
    }

    public static function registerStandalonePage($params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://' . $params['address'] . '/fbmessenger/registerstandalone/' . sha1(json_encode($params).'_'.erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['secret_hash']));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.29.0');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $content = curl_exec($ch);

        $http_error = '';

        if (curl_errno($ch)) {
            $http_error = curl_error($ch);
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpcode != 200) {
            throw new Exception('Request failed with '.$httpcode.' code. '.$http_error . '|' . $content);
        }

        return $content;
    }

    public static function processSubscribeOnMaster($params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['address'] . '/fbmessenger/registersubscribe/' . sha1(json_encode($params).'_'.erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['secret_hash']));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.29.0');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $content = curl_exec($ch);

        $http_error = '';

        if (curl_errno($ch)) {
            $http_error = curl_error($ch);
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpcode != 200) {
            throw new Exception('Request failed with '.$httpcode.' code. '.$http_error . '|' . $content);
        }

        return $content;
    }

    // We don't need verification as FB does that for us
    public static function proxyStandaloneRequest($params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $params['address']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.29.0');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(['Content-Type: application/json'],$params['headers']));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params['body']);
        $content = curl_exec($ch);
        return $content;
    }

}