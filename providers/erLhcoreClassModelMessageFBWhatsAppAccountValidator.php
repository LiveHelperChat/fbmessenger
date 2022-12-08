<?php

namespace LiveHelperChatExtension\fbmessenger\providers;

class erLhcoreClassModelMessageFBWhatsAppAccountValidator
{
    public static function validateAccount(\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount & $item)
    {
        $definition = array(
            'dep_id' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
            ),
            'name' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'access_token' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'active' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'business_account_id' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
            )
        );

        foreach ($item->phone_number_ids_array as $phoneNumberId) {
            $definition['dep_'.$phoneNumberId] = new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
            );
        }

        $form = new \ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        $phone_number_deps_array = $item->phone_number_deps_array;

        foreach ($item->phone_number_ids_array as $phoneNumberId) {
            if ( $form->hasValidData( 'dep_'.$phoneNumberId ))
            {
                $phone_number_deps_array[$phoneNumberId] = $form->{'dep_'.$phoneNumberId};
            } else if (isset($phone_number_deps_array[$phoneNumberId])) {
                unset($phone_number_deps_array[$phoneNumberId]);
            }
        }

        $item->phone_number_deps_array = $phone_number_deps_array;
        $item->phone_number_deps = json_encode($phone_number_deps_array);

        if ( $form->hasValidData( 'name' ) && $form->name != '')
        {
            $item->name = $form->name;
        } else {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('xmppservice/operatorvalidator','Please enter account name!');
        }

        if ( $form->hasValidData( 'dep_id' ))
        {
            $item->dep_id = $form->dep_id;
        } else {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('xmppservice/operatorvalidator','Please choose a department!');
        }

        if ( $form->hasValidData( 'access_token' ) && $form->access_token != '')
        {
            $item->access_token = $form->access_token;
        } else {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('xmppservice/operatorvalidator','Please enter an Access Token!');
        }

        if ( $form->hasValidData( 'business_account_id' ) && $form->business_account_id != '')
        {
            $item->business_account_id = $form->business_account_id;
        } else {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('xmppservice/operatorvalidator','Please enter a Business Account ID!');
        }

        if ( $form->hasValidData( 'active' ) && $form->active == true)
        {
            $item->active = 1;
        } else {
            $item->active = 0;
        }

        return $Errors;
    }
}

?>