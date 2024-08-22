<table class="table" ng-non-bindable>
        <thead>
            <th colspan="3"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Page')?></th>
        </thead>
    <?php foreach ($pages['data'] as $page) : $instagram = false?>
        <tr>
            <td width="60%">
                <div class="row">
                    <div class="col-4"><?php
                        $params = array (
                            'input_name'     => 'DepartmentID' . $page['id'],
                            'display_name'   => 'name',
                            'css_class'      => 'form-control form-control-sm',
                            'selected_id'    => isset($current_pages[$page['id']]) ? $current_pages[$page['id']]->dep_id : 0,
                            'list_function'  => 'erLhcoreClassModelDepartament::getList',
                            'list_function_params'  => array('limit' => '1000000')
                        );  echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?></div>
                    <div class="col-4">
                        <?php if (isset($current_pages[$page['id']]) && $current_pages[$page['id']]->enabled == 1) : ?>
                            <a class="btn btn-sm btn-danger btn-block csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/pagesubscribe')?>/<?php echo $page['id']?>/(action)/unsubscribe">Un Subscribe</a>
                            <?php if ($current_pages[$page['id']]->instagram_business_account != 0) : $instagram = true?><?php endif; ?>
                        <?php else : ?>
                            <a class="btn btn-sm btn-success btn-block" onclick="document.location = '<?php echo erLhcoreClassDesign::baseurl('fbmessenger/pagesubscribe')?>/<?php echo $page['id']?>/(dep)/'+$('#id_DepartmentID<?php echo $page['id']?>').val()+'/(csfr)/'+confLH.csrf_token;return false;" href="#"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Subscribe')?></a>
                        <?php endif; ?>
                    </div>
                    <div class="col-4">
                        <?php if (isset($current_pages[$page['id']])) : ?>
                            <input type="checkbox" onchange="$.post(WWW_DIR_JAVASCRIPT  +'fbmessenger/disablebot/<?php echo $current_pages[$page['id']]->id?>/' + $(this).is(':checked'), function(data) {});" name="bot_disabled" <?php $current_pages[$page['id']]->bot_disabled == 1 ? print 'checked="checked"' : '' ?> >&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Bot disabled')?>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
            <td nowrap="nowrap">
                <?php if (isset($current_pages[$page['id']]) && $current_pages[$page['id']]->enabled == 1) : ?>
                    <a class="btn btn-secondary ml-1 btn-xs csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/threadmy')?>/<?php echo $current_pages[$page['id']]->id?>/(action)/addbutton" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Add start button')?></a>
                    <a class="btn btn-secondary ml-1 btn-xs csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/threadmy')?>/<?php echo $current_pages[$page['id']]->id?>/(action)/rembutton" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Remove start button')?></a>
                <?php endif; ?>
            </td>
            <td width="40%" nowrap="nowrap">
                <?php if ($instagram == true) : ?>
                    <img class="img-fluid mr-2" style="height: 20px" src="/extension/chatto/design/chattotheme/images/social/instagram.png">
                <?php endif; ?>
                <?php echo htmlspecialchars($page['name'])?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (isset($phone_numbers) && !empty($phone_numbers)) : ?>
        <thead>
        <th colspan="3">WhatsApp phone numbers</th>
        </thead>

    <?php foreach ($phone_numbers as $phoneNumber) :?>
        <tr>
            <td width="60%" colspan="2">
                <div class="row">
                    <div class="col-4"><?php
                        $params = array (
                            'input_name'     => 'DepartmentID' . $phoneNumber['id'],
                            'display_name'   => 'name',
                            'css_class'      => 'form-control form-control-sm',
                            'selected_id'    => isset($current_pages_whatsapp[$phoneNumber['whatsapp_business_account_id']]) ? $current_pages_whatsapp[$phoneNumber['whatsapp_business_account_id']]->dep_id : 0,
                            'list_function'  => 'erLhcoreClassModelDepartament::getList',
                            'list_function_params'  => array('limit' => '1000000')
                        );  echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?></div>
                    <div class="col-4">

                        <?php if (isset($current_pages_whatsapp[$phoneNumber['whatsapp_business_account_id']]) && $current_pages_whatsapp[$phoneNumber['whatsapp_business_account_id']]->enabled == 1 && $phoneNumber['id'] == $current_pages_whatsapp[$phoneNumber['whatsapp_business_account_id']]->whatsapp_business_phone_number_id ) : ?>
                            <a class="btn btn-sm btn-danger btn-block" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/whatsappsubscribe')?>/<?php echo $phoneNumber['business_id']?>/<?php echo $phoneNumber['whatsapp_business_account_id']?>/<?php echo $phoneNumber['id']?>/(action)/unsubscribe">Un Subscribe</a>
                        <?php else : ?>
                            <a class="btn btn-sm btn-success btn-block" onclick="document.location = '<?php echo erLhcoreClassDesign::baseurl('fbmessenger/whatsappsubscribe')?>/<?php echo $phoneNumber['business_id']?>/<?php echo $phoneNumber['whatsapp_business_account_id']?>/<?php echo $phoneNumber['id']?>/(dep)/'+$('#id_DepartmentID<?php echo $phoneNumber['id']?>').val()+'/(action)/subscribe'" href="">Subscribe</a>
                        <?php endif; ?>

                        <?php /*<button type="button" disabled class="btn btn-sm btn-success btn-block">Subscribe is disabled at the moment</button>*/ ?>

                    </div>
                    <div class="col-4 fs12">
                        <span title="Phone number ID - <?php echo htmlspecialchars($phoneNumber['id']);?>"><?php echo htmlspecialchars($phoneNumber['display_phone_number']);?></span>, <?php echo htmlspecialchars($phoneNumber['verified_name']);?>, <?php echo htmlspecialchars($phoneNumber['code_verification_status']);?>
                    </div>
                </div>
            </td>
            <td width="40%" nowrap="nowrap">
                <span title="Business account ID - <?php echo htmlspecialchars($phoneNumber['business_id'])?>"><?php echo htmlspecialchars($phoneNumber['business_name'])?></span> [<span title="WhatsApp business account - <?php echo htmlspecialchars($phoneNumber['whatsapp_business_account_id'])?>"><?php echo htmlspecialchars($phoneNumber['whatsapp_business_account_name'])?></span>]
            </td>
        </tr>
    <?php endforeach; endif;?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>