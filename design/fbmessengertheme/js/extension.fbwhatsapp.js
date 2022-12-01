(function() {
    $('#template-to-send').change(function() {
        $.postJSON(WWW_DIR_JAVASCRIPT + '/fbwhatsapp/rendersend/' + $(this).val() + (businessAccountId ? '/' + businessAccountId : ''), {'data': JSON.stringify(messageFieldsValues)}, function(data) {
            $('#arguments-template').html(data.preview);
            $('#arguments-template-form').html(data.form);
        });
    });
    if ($('#template-to-send').val() != '') {
        $.postJSON(WWW_DIR_JAVASCRIPT + '/fbwhatsapp/rendersend/' + $('#template-to-send').val() + (businessAccountId ? '/' + businessAccountId : ''), {'data': JSON.stringify(messageFieldsValues)}, function(data) {
            $('#arguments-template').html(data.preview);
            $('#arguments-template-form').html(data.form);
        });
    }
})();

