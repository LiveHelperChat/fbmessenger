<table class="table">
    <thead>
    <th colspan="2">Page</th>
    </thead>
    <?php foreach ($pages['data'] as $page) : ?>
        <tr>
            <td width="20%">
                <div class="row">
                    <div class="col-xs-6"><?php
                        $params = array (
                            'input_name'     => 'DepartmentID' . $page['id'],
                            'display_name'   => 'name',
                            'css_class'      => 'form-control',
                            'selected_id'    => isset($current_pages[$page['id']]) ? $current_pages[$page['id']]->dep_id : 0,
                            'list_function'  => 'erLhcoreClassModelDepartament::getList',
                            'list_function_params'  => array('limit' => '1000000')
                        );  echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?></div>
                    <div class="col-xs-6">
                        <?php if (isset($current_pages[$page['id']]) && $current_pages[$page['id']]->enabled == 1) : ?>
                            <a class="btn btn-sm btn-danger btn-block" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/pagesubscribe')?>/<?php echo $page['id']?>/(action)/unsubscribe">Un Subscribe</a>
                        <?php else : ?>
                            <a class="btn btn-sm btn-success btn-block" onclick="document.location = '<?php echo erLhcoreClassDesign::baseurl('fbmessenger/pagesubscribe')?>/<?php echo $page['id']?>/(dep)/'+$('#id_DepartmentID<?php echo $page['id']?>').val()" href="">Subscribe</a>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
            <td width="99%" nowrap="nowrap">
                <?php echo htmlspecialchars($page['name'])?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>