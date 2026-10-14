$(document).ready(function(){
    $(".opt_delete_account a").click(function(){
        $("#dialog-delete-account").dialog('open');
    });

    $("#dialog-delete-account").dialog({
        autoOpen: false,
        modal: true,
        buttons: [
            {
                text: elektron.langs.delete,
                click: function() {
                    window.location = elektron.base_url + '?page=user&action=delete&id=' + elektron.user.id  + '&secret=' + elektron.user.secret;
                }
            },
            {
                text: elektron.langs.cancel,
                click: function() {
                    $(this).dialog("close");
                }
            }
        ]
    });
});