<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook pages list');?></h1>

<?php if (isset($items)) : ?>

<table cellpadding="0" cellspacing="0" class="table table-sm" width="100%">
<thead>
    <tr>   
        <th width="1%">ID</th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Page');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Callback URL');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Verified');?></th>
        <th width="1%"></th>
    </tr>
</thead>
    <?php foreach ($items as $item) : ?>
    <tr>
        <td><?php echo $item->id?></td>
        <td><?php echo $item->name?></td>
        <td><?php echo $item->callback_url?></td>
        <td><?php if ($item->verified == 1) : ?>Yes<?php else : ?>No<?php endif;?></td>
        <td nowrap="">
            <a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/thread')?>/<?php echo $item->id?>/(action)/addbutton" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Add start button')?></a>
            <a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/thread')?>/<?php echo $item->id?>/(action)/rembutton" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Remove start button')?></a>
        </td>
        <td nowrap>
          <div class="btn-group" role="group" aria-label="..." style="width:60px;">
            <a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/edit')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE254;</i></a>
            <a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/delete')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE872;</i></a>
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

<a href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/new')?>" class="btn btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Register new page');?></a>