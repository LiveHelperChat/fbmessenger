(function() {
    $('#template-to-send').change(function() {
        $.postJSON(WWW_DIR_JAVASCRIPT + '/fbwhatsapp/rendersend/' + $(this).val() + (typeof businessAccountId !== 'undefined' ? '/' + businessAccountId : ''), {'data': JSON.stringify(messageFieldsValues)}, function(data) {
            $('#arguments-template').html(data.preview);
            $('#arguments-template-form').html(data.form);
        });
    });
    if ($('#template-to-send').val() != '') {
        $.postJSON(WWW_DIR_JAVASCRIPT + '/fbwhatsapp/rendersend/' + $('#template-to-send').val() + (typeof businessAccountId !== 'undefined' ? '/' + businessAccountId : ''), {'data': JSON.stringify(messageFieldsValues)}, function(data) {
            $('#arguments-template').html(data.preview);
            $('#arguments-template-form').html(data.form);
        });
    }
    $('#id_business_account_id').change(function(){
        businessAccountId = $(this).val();
        $('#arguments-template').html('');
        $('#arguments-template-form').html('');
        $('#template-to-send').html('<option>Loading...</option>');
        $('#id_phone_sender_id').html('<option>Loading...</option>');
        $.postJSON(WWW_DIR_JAVASCRIPT + '/fbwhatsapp/rendertemplates/' +businessAccountId, function(data) {
            $('#template-to-send').html(data.templates);
            $('#id_phone_sender_id').html(data.phones);
        });
    });
})();

var fbwhatsapp = {
    attachFiles : function() {
        $('.choose-file').click( function() {
            console.log('attach file clicked');
        });
    }
};

