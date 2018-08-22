$(document).ready(function () {

    /**
     * Clean modal window after closing
     */
    $('.modal').on('hide.bs.modal', function() {
        $(this).removeData();
    });

    $(document).on('submit','#leave-message-form',function(){
        $('.button-submitter').click();
        return false;
    });

    /**
     * Overriding submit action for form (to send via ajax) and reload modal (or table if returned OK)
     */
    $(document).on('click','[data-ajax-form]',function(){

        var form = $($(this).data('ajax-form'));
        var formData = new FormData(form[0]);
        var okReload = $($(this).data('ok-reload'));

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            async: false,
            success: function (data) {
                if(data != 'OK'){
                    var modalContent = $('.modal-content');
                    modalContent.html(data);

                    var chatBox = modalContent.find('.direct-chat-messages');
                    if(chatBox){
                        chatBox[0].scrollTop = chatBox[0].scrollHeight;
                    }
                }else{
                    $.ajax({
                        url: okReload.data('reload-url'),
                        type: 'GET',
                        async: false,
                        success: function(reloaded_data){
                            okReload.html(reloaded_data);
                            $('.modal').modal('hide');
                        }
                    });
                }
            },
            cache: false,
            contentType: false,
            processData: false
        });

        return false;
    });

    /**
     * Reloading links (updates container's html via ajax)
     */
    $(document).on('click','[data-ajax-reloader]', function(){

        var confirmMsg = $(this).data('confirm-ajax');

        if(confirmMsg){
            if(!confirm(confirmMsg)){
                return false;
            }
        }

        var container = $($(this).data('ajax-reloader'));
        if($(this).data('load-parent') == 'yes'){
            container = container.parent();
        }

        var link = $(this);

        $.ajax({
            url: link.attr('href'),
            type: 'GET',
            async: false,
            success: function(reloaded_data){
                container.html(reloaded_data);
            }
        });

        return false;
    });
});

