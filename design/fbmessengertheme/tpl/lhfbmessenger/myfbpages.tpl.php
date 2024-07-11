<table class="table" ng-non-bindable>
    <thead>
        <th colspan="3"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Page')?></th>
    </thead>
    <?php foreach ($pages['data'] as $page) : ?>
        <tr>
            <td width="20%">
                <div class="row">
                    <div class="col-6"><?php
                        $params = array (
                            'input_name'     => 'DepartmentID' . $page['id'],
                            'display_name'   => 'name',
                            'css_class'      => 'form-control form-control-sm',
                            'selected_id'    => isset($current_pages[$page['id']]) ? $current_pages[$page['id']]->dep_id : 0,
                            'list_function'  => 'erLhcoreClassModelDepartament::getList',
                            'list_function_params'  => array('limit' => '1000000')
                        );  echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?></div>
                    <div class="col-6">
                        <?php if (isset($current_pages[$page['id']]) && $current_pages[$page['id']]->enabled == 1) : ?>
                            <a class="btn btn-sm btn-danger btn-block csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/pagesubscribe')?>/<?php echo $page['id']?>/(action)/unsubscribe"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Un Subscribe')?></a>
                        <?php else : ?>
                            <a class="btn btn-sm btn-success btn-block" onclick="document.location = '<?php echo erLhcoreClassDesign::baseurl('fbmessenger/pagesubscribe')?>/<?php echo $page['id']?>/(dep)/'+$('#id_DepartmentID<?php echo $page['id']?>').val()+'/(csfr)/'+confLH.csrf_token;return false;" href="#"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Subscribe')?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
            <td width="1%" style="white-space: nowrap">
                <?php if (isset($current_pages[$page['id']])) : ?>
                    <input type="checkbox" onchange="$.post(WWW_DIR_JAVASCRIPT  +'fbmessenger/disablebot/<?php echo $current_pages[$page['id']]->id?>/' + $(this).is(':checked'), function(data) {});" name="bot_disabled" <?php $current_pages[$page['id']]->bot_disabled == 1 ? print 'checked="checked"' : '' ?> >&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Bot disabled')?>
                <?php endif; ?>
            </td>
            <td width="99%" nowrap="nowrap">
                <?php echo htmlspecialchars($page['name'])?>

                <?php if (isset($current_pages[$page['id']]) && $current_pages[$page['id']]->enabled == 1) : ?>
                    <a class="btn btn-secondary ml-1 btn-xs csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/threadmy')?>/<?php echo $current_pages[$page['id']]->id?>/(action)/addbutton" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Add start button')?></a>
                    <a class="btn btn-secondary ml-1 btn-xs csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/threadmy')?>/<?php echo $current_pages[$page['id']]->id?>/(action)/rembutton" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Remove start button')?></a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>