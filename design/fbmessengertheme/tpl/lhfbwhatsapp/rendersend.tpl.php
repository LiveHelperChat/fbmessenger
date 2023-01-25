<?php /*
<pre><?php print_r($template);?></pre>
*/ ?>

<h6><?php echo htmlspecialchars($template['name'])?> <span class="badge badge-secondary"><?php echo htmlspecialchars($template['category'])?></span></h6>
<?php $fieldsCount = 0;$fieldsCountHeader = 0;$fieldCountHeaderDocument = 0;$fieldCountHeaderImage = 0;$fieldCountHeaderVideo = 0;?>
<div class="rounded bg-light p-2" title="<?php echo htmlspecialchars(json_encode($template, JSON_PRETTY_PRINT))?>">
    <?php foreach ($template['components'] as $component) : ?>
        <?php if ($component['type'] == 'HEADER' && $component['format'] == 'IMAGE' && isset($component['example']['header_url'][0])) : ?>
            <img src="<?php echo htmlspecialchars($component['example']['header_url'][0])?>" />
        <?php endif; ?>
        <?php if ($component['type'] == 'HEADER' && $component['format'] == 'DOCUMENT' && isset($component['example']['header_url'][0])) : ?>
            <div>
                <span class="badge badge-secondary">FILE: <?php echo htmlspecialchars($component['example']['header_url'][0])?></span>
            </div>
        <?php endif; ?>
        <?php if ($component['type'] == 'HEADER' && $component['format'] == 'VIDEO' && isset($component['example']['header_url'][0])) : ?>
            <div>
                <span class="badge badge-secondary">VIDEO: <?php echo htmlspecialchars($component['example']['header_url'][0])?></span>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php foreach ($template['components'] as $component) : ?>
        <?php if ($component['type'] == 'BODY') :
            $matchesReplace = [];
            preg_match_all('/\{\{[0-9]\}\}/is',$component['text'],$matchesReplace);
            if (isset($matchesReplace[0])) {
                $fieldsCount = count($matchesReplace[0]);
            }
            ?><p><?php echo htmlspecialchars($component['text'])?></p><?php endif; ?>
        <?php if ($component['type'] == 'HEADER') : ?>
            <?php if ($component['format'] == 'DOCUMENT') : $fieldCountHeaderDocument = 1;?>
                <h5 class="text-secondary">DOCUMENT</h5>
            <?php elseif ($component['format'] == 'VIDEO') : $fieldCountHeaderVideo = 1;?>
                <h5 class="text-secondary">VIDEO</h5>
                <?php if (isset($component['example']['header_handle'][0])) : ?>
                    <video width="100">
                        <source src="<?php echo htmlspecialchars($component['example']['header_handle'][0])?>" type="video/mp4">
                    </video>
                <?php endif; ?>
            <?php elseif ($component['format'] == 'IMAGE') : $fieldCountHeaderImage = 1;?>
                <h5 class="text-secondary">IMAGE</h5>
                <?php if (isset($component['example']['header_handle'][0])) : ?>
                    <img src="<?php echo htmlspecialchars($component['example']['header_handle'][0])?>" />
                <?php endif; ?>
            <?php else : ?>
                <?php
                $matchesReplace = [];
                preg_match_all('/\{\{[0-9]\}\}/is',$component['text'],$matchesReplace);
                if (isset($matchesReplace[0])) {
                    $fieldsCountHeader = count($matchesReplace[0]);
                }
                ?>
                <h5 class="text-secondary"><?php echo htmlspecialchars($component['text'])?></h5>
            <?php endif; ?>

        <?php endif; ?>
        <?php if ($component['type'] == 'FOOTER') : ?><p class="text-secondary"><?php echo htmlspecialchars($component['text'])?></p><?php endif; ?>
        <?php if ($component['type'] == 'BUTTONS') : ?>
            <?php foreach ($component['buttons'] as $button) : ?>
                <div class="pb-2"><button class="btn btn-sm btn-secondary"><?php echo htmlspecialchars($button['text'])?> | <?php echo htmlspecialchars($button['type'])?></button></div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<!--=========||=========-->
<div class="row">
    <?php for ($i = 0; $i < $fieldsCount; $i++) : ?>
        <div class="col-6" ng-non-bindable>
            <div class="form-group">
                <label class="font-weight-bold">Body Text - {{<?php echo $i+1?>}}</label>
                <input type="text" list="fields_placeholders" class="form-control form-control-sm" name="field_<?php echo $i+1?>" value="<?php if (isset($data['field_' .  $i + 1])) : ?><?php echo htmlspecialchars($data['field_' .  $i + 1])?><?php endif; ?>">
            </div>
        </div>
    <?php endfor; ?>
    <?php for ($i = 0; $i < $fieldsCountHeader; $i++) : ?>
        <div class="col-6" ng-non-bindable>
            <div class="form-group">
                <label class="font-weight-bold">Header Text - {{<?php echo $i+1?>}}</label>
                <input type="text" list="fields_placeholders" class="form-control form-control-sm" name="field_header_<?php echo $i+1?>" value="<?php if (isset($data['field_header_' .  $i + 1])) : ?><?php echo htmlspecialchars($data['field_header_' .  $i + 1])?><?php endif; ?>">
            </div>
        </div>
    <?php endfor; ?>

    <?php for ($i = 0; $i < $fieldCountHeaderDocument; $i++) : ?>
        <div class="col-6" ng-non-bindable>
            <div class="form-group">
                <label class="font-weight-bold">Document URL - {{<?php echo $i+1?>}}</label>
                <input type="text" list="fields_placeholders" class="form-control form-control-sm" placeholder="https://example.com/filename.pdf" name="field_header_doc_<?php echo $i+1?>" value="<?php if (isset($data['field_header_doc_' .  $i + 1])) : ?><?php echo htmlspecialchars($data['field_header_doc_' .  $i + 1])?><?php endif; ?>">
                <label class="font-weight-bold">Filename - {{<?php echo $i+1?>}}</label>
                <input list="fields_placeholders" type="text" class="form-control form-control-sm" placeholder="filename.pdf" name="field_header_doc_filename_<?php echo $i+1?>" value="<?php if (isset($data['field_header_doc_filename_' .  $i + 1])) : ?><?php echo htmlspecialchars($data['field_header_doc_filename_' .  $i + 1])?><?php endif; ?>">
            </div>
        </div>
    <?php endfor; ?>

    <?php for ($i = 0; $i < $fieldCountHeaderImage; $i++) : ?>
        <div class="col-6" ng-non-bindable>
            <div class="form-group">
                <label class="font-weight-bold">Header image URL - {{<?php echo $i+1?>}}</label>
                <input list="fields_placeholders" type="text" class="form-control form-control-sm" placeholder="https://example.com/image.png" name="field_header_img_<?php echo $i+1?>" value="<?php if (isset($data['field_header_img_' .  $i + 1])) : ?><?php echo htmlspecialchars($data['field_header_img_' .  $i + 1])?><?php endif; ?>">
            </div>
        </div>
    <?php endfor; ?>

    <?php for ($i = 0; $i < $fieldCountHeaderVideo; $i++) : ?>
        <div class="col-6" ng-non-bindable>
            <div class="form-group">
                <label class="font-weight-bold">Header video URL - {{<?php echo $i+1?>}}</label>
                <input list="fields_placeholders" type="text" class="form-control form-control-sm" placeholder="https://example.com/video.mp4" name="field_header_video_<?php echo $i+1?>" value="<?php if (isset($data['field_header_video_' .  $i + 1])) : ?><?php echo htmlspecialchars($data['field_header_video_' .  $i + 1])?><?php endif; ?>">
            </div>
        </div>
    <?php endfor; ?>

    <datalist id="fields_placeholders">
        <option value="{args.recipient.name_front}">Name</option>
        <option value="{args.recipient.lastname_front}">Lastname</option>
        <option value="{args.recipient.company_front}">Company</option>
        <option value="{args.recipient.title_front}">Title</option>
        <option value="{args.recipient.email_front}">E-Mail</option>
        <option value="{args.recipient.file_1_url_front}">File 1</option>
        <option value="{args.recipient.file_2_url_front}">File 2</option>
        <option value="{args.recipient.file_3_url_front}">File 3</option>
        <option value="{args.recipient.file_4_url_front}">File 4</option>
        <option value="{args.recipient.attr_str_1_front}">String attribute 1</option>
        <option value="{args.recipient.attr_str_2_front}">String attribute 2</option>
        <option value="{args.recipient.attr_str_3_front}">String attribute 3</option>
        <option value="{args.recipient.attr_str_4_front}">String attribute 4</option>
        <option value="{args.recipient.attr_str_5_front}">String attribute 5</option>
        <option value="{args.recipient.attr_str_6_front}">String attribute 6</option>
    </datalist>

</div>

<?php /*<pre><?php echo json_encode($template, JSON_PRETTY_PRINT)?></pre>*/ ?>
