<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Templates'); ?></h1>

<table class="table table-sm" ng-non-bindable>
    <?php foreach ($templates as $template) : ?>
        <tr>
            <td>
                <?php echo htmlspecialchars($template['name'])?>
            </td>
            <td>
                <?php echo htmlspecialchars($template['language'])?>
            </td>
            <td>
                <?php echo htmlspecialchars($template['status'])?>
            </td>
            <td>
                <?php echo htmlspecialchars($template['category'])?>
            </td>
            <td>
                <textarea class="form-control form-control-sm fs12"><?php echo htmlspecialchars(json_encode($template['components'],JSON_PRETTY_PRINT)); ?></textarea>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
