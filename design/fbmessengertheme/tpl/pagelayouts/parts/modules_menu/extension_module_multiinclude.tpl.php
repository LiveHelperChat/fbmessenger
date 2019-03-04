<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhfbmessenger','use_admin')) : ?>
<li class="nav-item"><a class="nav-link" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/index')?>"><i class="material-icons">comment</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Facebook chat');?></a></li>
<?php endif; ?>