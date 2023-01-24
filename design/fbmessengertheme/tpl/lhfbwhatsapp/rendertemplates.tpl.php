<option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Choose a template');?></option>
<?php foreach ($templates as $template) : ?>
    <option value="<?php echo htmlspecialchars($template['name'] . '||' . $template['language'] . '||' . $template['id'])?>"><?php echo htmlspecialchars($template['name'] . ' [' . $template['language'] . ']')?></option>
<?php endforeach; ?>
<!--=========||=========-->
<?php foreach ($phones as $phone) : ?>
    <option value="<?php echo $phone['id']?>" >
        <?php echo $phone['display_phone_number'],' | ', $phone['verified_name'],' | ', $phone['code_verification_status'],' | ', $phone['quality_rating']?>
    </option>
<?php endforeach; ?>