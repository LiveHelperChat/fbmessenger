<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsappmessaging/importfrommailinglist.tpl.php');

$campaign = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::fetch($Params['user_parameters']['id']);

if (ezcInputForm::hasPostData() && !(!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token']))) {

    $definition = array(
        'ml' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1),FILTER_REQUIRE_ARRAY
        ),
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    $statistic = ['skipped' => 0, 'already_exists' => 0, 'imported' => 0, 'unassigned' => 0];

    $listPrivate = erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsappmessaging','all_contact_list');

    if ($form->hasValidData( 'ml' ) && !empty($form->ml)) {
        foreach ($form->ml as $ml) {
            foreach (\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContactListContact::getList(['limit' => false, 'filter' => ['contact_list_id' => $ml]]) as $mailingRecipient) {

                // Skip private contact in public list
                if ($listPrivate === false && $mailingRecipient->private == 1 && $mailingRecipient->user_id != (int)\erLhcoreClassUser::instance()->getUserID()) {
                    $statistic['skipped']++;
                    continue;
                }

                if (isset($_POST['export_action']) && $_POST['export_action'] == 'unassign') {
                    if ($mailingRecipient->mailing_recipient instanceof \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact) {
                        foreach (\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::getList(['filter' => ['campaign_id' => $campaign->id, 'phone' => $mailingRecipient->mailing_recipient->phone]]) as $campaignRecipient) {
                            $campaignRecipient->removeThis();
                            $statistic['unassigned']++;
                        }
                    }
                    continue;
                }

                if (!($mailingRecipient->mailing_recipient instanceof \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact) || $mailingRecipient->mailing_recipient->disabled == 1) {
                    $statistic['skipped']++;
                    continue;
                }

                if (\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::getCount(['filter' => ['campaign_id' => $campaign->id, 'phone' => $mailingRecipient->mailing_recipient->phone]]) == 1) {
                    $statistic['already_exists']++;
                    continue;
                }

                $campaignRecipient = new \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient();
                $campaignRecipient->campaign_id = $campaign->id;
                $campaignRecipient->recipient_id = $mailingRecipient->contact_id;
                $campaignRecipient->email = $mailingRecipient->mailing_recipient->email;
                $campaignRecipient->phone = $mailingRecipient->mailing_recipient->phone;
                $campaignRecipient->saveThis();

                $statistic['imported']++;
            }
        }

        $tpl->set('statistic', $statistic);
        $tpl->set('updated', true);

    } else {
        $tpl->set('errors', ['Please choose at-least one mailing list']);
    }
}

$tpl->set('item', $campaign);
$tpl->set('action_url', erLhcoreClassDesign::baseurl('fbwhatsappmessaging/importfrommailinglist') . '/' . $campaign->id);

echo $tpl->fetch();
exit;
