<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Quick stats');
$modalSize = 'ml';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<div class="modal-body">
    <div class="row">
        <?php foreach (\LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingValidator::getStatus()  as $statVariation) : ?>
        <div class="col-6">
            <div class="form-group">
                <h6><?php echo htmlspecialchars($statVariation->name)?></h6>
                <?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::getCount(array_merge_recursive($filter,['filter' => ['status' => $statVariation->id]])); ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>