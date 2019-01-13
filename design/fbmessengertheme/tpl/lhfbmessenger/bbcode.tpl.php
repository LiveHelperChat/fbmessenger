<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook BBCode List');?></h1>

<?php if (isset($items)) : ?>

<table cellpadding="0" cellspacing="0" class="table" width="100%">
    <thead>
    <tr>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Name');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','BBCode');?></th>
        <th width="1%"></th>
    </tr>
    </thead>
    <?php foreach ($items as $item) : ?>
        <tr>
            <td><?php echo $item->name?></td>
            <td>[<?php echo $item->bbcode?>]</td>
            <td nowrap>
                <div class="btn-group" role="group" aria-label="..." style="width:60px;">
                    <a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/editbbcode')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE254;</i></a>
                    <a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/deletebbcode')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE872;</i></a>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php endif;?>

<a href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/newbbcode')?>" class="btn btn-default"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Register new BBCode');?></a>