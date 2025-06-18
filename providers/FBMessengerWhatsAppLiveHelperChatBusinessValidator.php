<?php

namespace LiveHelperChatExtension\fbmessenger\providers;
#[\AllowDynamicProperties]
class FBMessengerWhatsAppLiveHelperChatBusinessValidator {

    private static $phoneNumbers = [];

    public static function getBusinessAccounts($params)
    {
        $limitation = $params['limitation'];

        $renderOptions = array (
            'list_function_params'  => array('limit' => false, 'filter' => ['active' => 1]),
        );

        if (!empty($limitation['business_accounts'])) {
            $renderOptions['list_function_params'] = ['filterin' => ['id' => $limitation['business_accounts']]];
        }

        $businessAccounts = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::getList($renderOptions['list_function_params']);

        $user = \erLhcoreClassModelFBMessengerUser::findOne(array('filter' => array('user_id' => \erLhcoreClassUser::instance()->getUserID())));

        if ($user instanceof \erLhcoreClassModelFBMessengerUser) {
            try {
                $fb = \erLhcoreClassModelFBMessengerUser::getFBApp();

                try {
                    $response = $fb->get('me?fields=businesses');
                    $responseData = $response->getDecodedBody();
                    $phoneNumbers = [];

                    foreach ($responseData['businesses']['data'] as $dataItem) {
                        try {
                            $response = $fb->get($dataItem['id'].  '/owned_whatsapp_business_accounts');
                            $whatsAppBusinessAccounts = $response->getDecodedBody();

                            foreach ($whatsAppBusinessAccounts['data'] as $whatsAppBusinessAccount) {

                                $businessAccountsItem = new \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount();

                                $businessAccountsItem->name = $whatsAppBusinessAccount['name'];
                                $businessAccountsItem->business_account_id = $whatsAppBusinessAccount['id'];
                                $businessAccountsItem->id = 'whatsapp-' . $whatsAppBusinessAccount['id'];

                                $businessAccounts[] = $businessAccountsItem;

                                $response = $fb->get($whatsAppBusinessAccount['id'] . '/phone_numbers');
                                $phoneNumbersData = $response->getDecodedBody();
                                foreach ($phoneNumbersData['data'] as $phoneNumber) {
                                    $phoneNumber['whatsapp_business_account_id'] = $whatsAppBusinessAccount['id'];
                                    $phoneNumber['whatsapp_business_account_name'] = $whatsAppBusinessAccount['name'];
                                    $phoneNumber['business_id'] = $dataItem['id'];
                                    $phoneNumber['business_name'] = $dataItem['name'];

                                    self::$phoneNumbers[$whatsAppBusinessAccount['id']]['phones'][] = $phoneNumber;
                                }
                            }
                        } catch (\Exception $e) {

                        }
                    }
                } catch (\Exception $e){ // Not all busienss we can manage
                    //print_r( $e);
                }
            } catch (\Exception $e) {
                //print_r( $e);
            }
        }

        return $businessAccounts;
    }


    public static function getFirstBusinessAccountId()
    {
        $fb = \erLhcoreClassModelFBMessengerUser::getFBApp();
        try {
            $response = $fb->get('me?fields=businesses');
            $responseData = $response->getDecodedBody();

            foreach ($responseData['businesses']['data'] as $dataItem) {
                try {
                    $response = $fb->get($dataItem['id'].  '/owned_whatsapp_business_accounts');
                    $whatsAppBusinessAccounts = $response->getDecodedBody();
                    foreach ($whatsAppBusinessAccounts['data'] as $whatsAppBusinessAccount) {
                        return $whatsAppBusinessAccount['id'];
                    }
                } catch (\Exception $e) {

                }
            }
        } catch (\Exception $e) {
            // Handle exception
        }
    }

    /**
     * Returns phone numbers for a given business account ID.
     *
     * @param string $businessAccountId
     * @return array
     */
    public static function getPhoneNumbers($businessAccountId)
    {
        if (isset(self::$phoneNumbers[$businessAccountId])) {
            return self::$phoneNumbers[$businessAccountId]['phones'];
        } else {
            
            $fb = \erLhcoreClassModelFBMessengerUser::getFBApp();

            try {
                $response = $fb->get($businessAccountId . '/phone_numbers');
                $phoneNumbersData = $response->getDecodedBody();
                self::$phoneNumbers[$businessAccountId]['phones'] = [];

                foreach ($phoneNumbersData['data'] as $phoneNumber) {
                    self::$phoneNumbers[$businessAccountId]['phones'][] = $phoneNumber;
                }

                return self::$phoneNumbers[$businessAccountId]['phones'];
            } catch (\Exception $e) {
                return [];
            }
        }
    }

    public static function getTemplates($businessAccountId) {
        $fb = \erLhcoreClassModelFBMessengerUser::getFBApp();
         try {

            $response = $fb->get("{$businessAccountId}/message_templates");
            $templates = $response->getDecodedBody();

            if (isset($templates['data']) && is_array($templates['data'])) {
                return $templates['data'];
            } else {
                throw new \Exception('Could not fetch templates - ' . print_r( $templates, true),100);
            }
        } catch (\Exception $e) {
            throw new \Exception('Could not fetch templates - ' . $e->getMessage(),100);
        }
    }

    public static function getTemplate($tempalteId, $language) {
        $fb = \erLhcoreClassModelFBMessengerUser::getFBApp();
        $response = $fb->get($tempalteId);
        $template = $response->getDecodedBody();
        return $template;
    }

}

?>