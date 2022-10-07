<?php

namespace LiveHelperChatExtension\fbmessenger\providers {

    class FBMessengerWhatsAppLiveHelperChat {

        public static function getInstance() {

            if (self::$instance !== null){
                return self::$instance;
            }

            self::$instance = new self();

            return self::$instance;
        }

        public function __construct() {
            $mbOptions = \erLhcoreClassModelChatConfig::fetch('fbmessenger_options');
            $data = (array)$mbOptions->data;

            if (!isset($data['whatsapp_access_token']) || empty($data['whatsapp_access_token'])) {
                throw new \Exception('Access Key is not set!',100);
            }

            if (!isset($data['whatsapp_business_account_id']) || empty($data['whatsapp_business_account_id'])) {
                throw new \Exception('WhatsApp Business Account ID',100);
            }

            $this->access_key = $data['whatsapp_access_token'];
            $this->whatsapp_business_account_id = $data['whatsapp_business_account_id'];
            $this->endpoint = 'https://graph.facebook.com/';
        }

        public function getPhones() {
            // https://developers.facebook.com/docs/graph-api/reference/whats-app-business-account/phone_numbers/
            // curl -i -X GET "https://graph.facebook.com/LATEST-VERSION/WHATSAPP-BUSINESS-ACCOUNT-ID/phone_numbers?access_token=USER-ACCESS-TOKEN"
            $templates = $this->getRestAPI([
                'baseurl'   => $this->endpoint,
                'bearer'    => $this->access_key,
                'method'    => "v15.0/{$this->whatsapp_business_account_id}/phone_numbers",
            ]);

            if (isset($templates['data']) && is_array($templates['data'])) {
                return $templates['data'];
            } else {
                throw new \Exception('Could not fetch phone numbers - ' . print_r( $templates, true),100);
            }
        }

        public function getTemplates() {
            // https://developers.facebook.com/docs/graph-api/reference/whats-app-business-account/message_templates/
            // curl -i -X GET "https://graph.facebook.com/LATEST-VERSION/WHATSAPP-BUSINESS-ACCOUNT-ID/message_templates?access_token=USER-ACCESS-TOKEN"
            // curl -i -X GET "https://graph.facebook.com/v15.0/105209658989864/message_templates?access_token=EAARB6lT6poQBAPgBHm06sO7QfAZAPjflwCRuLRCKHnT9I9g9ZCeDqQ5bLktX647qH2JwWmMWD1kijbReD5ZASZAdJZCFgIyN5NJ1lkzhjwsibYDSwN5a6YhZCUgMgZCbl52am5Q8pXLatXmTp4yxL1kdhDC3DTai1MU7Ujmo1suscwjwoSPgR71"

            $templates = $this->getRestAPI([
                'baseurl'   => $this->endpoint,
                'bearer'    => $this->access_key,
                'method'    => "v15.0/{$this->whatsapp_business_account_id}/message_templates",
            ]);

            if (isset($templates['data']) && is_array($templates['data'])) {
                return $templates['data'];
            } else {
                throw new \Exception('Could not fetch templates - ' . print_r( $templates, true),100);
            }
        }

        public function getTemplate($name, $language) {
            // https://developers.facebook.com/docs/graph-api/reference/whats-app-business-hsm/
            // curl -i -X GET "https://graph.facebook.com/LATEST-VERSION/WHATS-APP-MESSAGE-TEMPLATE-ID?access_token=USER-ACCESS-TOKEN"
            return $this->getRestAPI([
                'baseurl' => $this->endpoint,
                'bearer' =>  $this->access_key,
                'method' => "v15.0/{$name}",
            ]);
        }


        public function sendTemplate($item, $templates = [], $phones = []) {

            $argumentsTemplate = [];

            $templatePresent = null;
            foreach ($templates as $template) {
                if ($template['name'] == $item->template && $template['language'] == $item->language) {
                    $templatePresent = $template;
                    $item->template_id = $template['id'];
                }
            }

            foreach ($phones as $phone) {
                if ($item->phone_sender_id == $phone['id']) {
                    $item->phone_sender = $phone['display_phone_number'];
                }
            }

            // Extract phone sender number and store as phone_sender attribute
            $bodyArguments = [];

            // https://developers.facebook.com/docs/whatsapp/on-premises/reference/messages#template-object
            $bodyText = '';
            foreach ($templatePresent['components'] as $component) {
                if ($component['type'] == 'BODY') {
                    $bodyText = $component['text'];
                } elseif ($component['type'] == 'BUTTONS') {
                    foreach ($component['buttons'] as $indexButton => $button) {
                        if ($button['type'] == 'QUICK_REPLY') {
                            $bodyArguments[] = [
                                "type" => "button",
                                "sub_type" => "quick_reply",
                                "index" => (int)$indexButton,
                                "parameters" => [
                                    [
                                        "type" => "payload",
                                        "payload" => $item->template.'-quick_reply_'.$indexButton,
                                    ]
                                ]
                            ];
                        }
                    }
                } elseif ($component['type'] == 'HEADER' && $component['format'] == 'VIDEO') {
                    $bodyArguments[] = [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type"=> "video",
                                "video"=> [
                                    "link"=> (isset($component['example']['header_url'][0]) ? $component['example']['header_url'][0] : 'https://omni.enviosok.com/design/defaulttheme/images/general/logo.png'),
                                ]
                            ]
                        ]
                    ];
                } elseif ($component['type'] == 'HEADER' && $component['format'] == 'DOCUMENT') {
                    $bodyArguments[] = [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type"=> "document",
                                "document"=> [
                                    "link"=> (isset($component['example']['header_url'][0]) ? $component['example']['header_url'][0] : 'https://omni.enviosok.com/design/defaulttheme/images/general/logo.png'),
                                ]
                            ]
                        ]
                    ];
                } elseif ($component['type'] == 'HEADER' && $component['format'] == 'IMAGE') {
                    $bodyArguments[] = [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type"=> "image",
                                "image"=> [
                                    "link"=> (isset($component['example']['header_url'][0]) ? $component['example']['header_url'][0] : 'https://omni.enviosok.com/design/defaulttheme/images/general/logo.png'),
                                ]
                            ]
                        ]
                    ];
                }
            }

            $item->message = $bodyText;

            $messageVariables = $item->message_variables_array;

            for ($i = 0; $i < 6; $i++) {
                if (isset($messageVariables['field_' . $i]) && $messageVariables['field_' . $i] != '') {
                    $item->message = str_replace('{{'.$i.'}}', $messageVariables['field_' . $i], $item->message);
                    $argumentsTemplate[] = ['type' => 'text','text' => $messageVariables['field_' . $i]];
                }
            }

            if (!empty($argumentsTemplate)) {
                $bodyArguments[] = [
                    'type' => 'body',
                    'parameters' => $argumentsTemplate,
                ];
            }

            $requestParams = [
                'baseurl' => $this->endpoint,
                'method' => "v15.0/{$item->phone_sender_id}/messages",
                'bearer' => $this->access_key,
                'body_json' => json_encode([
                    'messaging_product' => 'whatsapp',
                    'to' => $item->phone,
                    'type' => 'template',
                    'template' => [
                        'name' => $item->template,
                        'language' => [
                            'policy' => 'deterministic',
                            'code' => $item->language
                        ],
                        'components' => $bodyArguments
                    ],
                ])
            ];

            $response = null;

            try {

                $response = $this->getRestAPI($requestParams);

                // Responder
                if (isset($response['messages'][0]['id'])) {
                    $item->fb_msg_id = $response['messages'][0]['id'];
                } else {
                    throw new \Exception('Message ID was not returned.');
                }

                $item->send_status_raw = json_encode($response);
                $item->saveThis();

            } catch (\Exception $e) {
                $item->send_status_raw = json_encode($response) . $e->getTraceAsString() . $e->getMessage();
                $item->status = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_FAILED;
                $item->saveThis();
            }
        }

        public function getRestAPI($params)
        {
            $try = isset($params['try']) ? $params['try'] : 3;

            for ($i = 0; $i < $try; $i++) {

                $ch = curl_init();
                $url = rtrim($params['baseurl'], '/') . '/' . $params['method'] . (isset($params['args']) ? '?' . http_build_query($params['args']) : '');

                if (!isset(self::$lastCallDebug['request_url'])) {
                    self::$lastCallDebug['request_url'] = array();
                }

                if (!isset(self::$lastCallDebug['request_url_response'])) {
                    self::$lastCallDebug['request_url_response'] = array();
                }

                self::$lastCallDebug['request_url'][] = $url;

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, self::$apiTimeout);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                if (isset($params['method_type']) && $params['method_type'] == 'delete') {
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                }

                $headers = array(
                    'Accept: application/json',
                    'Authorization: AccessKey ' . $this->access_key
                );

                if (isset($params['body_json']) && !empty($params['body_json'])) {
                    curl_setopt($ch, CURLOPT_POST,1 );
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params['body_json']);
                    $headers[] = 'Content-Type: application/json';
                    $headers[] = 'Expect:';
                }

                if (isset($params['bearer']) && !empty($params['bearer'])) {
                    $headers[] = 'Authorization: Bearer ' . $params['bearer'];
                }

                if (isset($params['headers']) && !empty($params['headers'])) {
                    $headers = array_merge($headers, $params['headers']);
                }

                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

                $startTime = date('H:i:s');
                $additionalError = ' ';

                if (isset($params['test_mode']) && $params['test_mode'] == true) {
                    $content = $params['test_content'];
                    $httpcode = 200;
                } else {
                    $content = curl_exec($ch);

                    if (curl_errno($ch))
                    {
                        $additionalError = ' [ERR: '. curl_error($ch).'] ';
                    }

                    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                }

                $endTime = date('H:i:s');

                if (isset($params['log_response']) && $params['log_response'] == true) {
                    self::$lastCallDebug['request_url_response'][] = '[T' . self::$apiTimeout . '] ['.$httpcode.']'.$additionalError.'['.$startTime . ' ... ' . $endTime.'] - ' . ((isset($params['body_json']) && !empty($params['body_json'])) ? $params['body_json'] : '') . ':' . $content;
                }

                if ($httpcode == 204) {
                    return array();
                }

                if ($httpcode == 404) {
                    throw new \Exception('Resource could not be found!');
                }

                if (isset($params['return_200']) && $params['return_200'] == true && $httpcode == 200) {
                    return $content;
                }

                if ($httpcode == 401) {
                    throw new \Exception('No permission to access resource!');
                }

                if ($content !== false)
                {
                    if (isset($params['raw_response']) && $params['raw_response'] == true){
                        return $content;
                    }

                    $response = json_decode($content,true);
                    if ($response === null) {
                        if ($i == 2) {
                            throw new \Exception('Invalid response was returned. Expected JSON');
                        }
                    } else {
                        if ($httpcode != 500) {
                            return $response;
                        }
                    }

                } else {
                    if ($i == 2) {
                        throw new \Exception('Invalid response was returned');
                    }
                }

                if ($httpcode == 500 && $i >= 2) {
                    throw new \Exception('Invalid response was returned');
                }

                usleep(300);
            }
        }

        private $endpoint = null;
        private $access_key = null;
        private $whatsapp_business_account_id = null;

        private static $instance = null;
        public static $lastCallDebug = array();
        public static $apiTimeout = 40;
    }
}