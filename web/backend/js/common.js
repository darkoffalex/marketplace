$(document).ready(function () {

    /**
     * Ссылки, переход на которые осуществляется ajax-ом
     * а содержимое отданных документов вписывается в какой-либо эл-мент
     */
    $(document).on('click','[data-ajax-reloader]', function(){

        //Сообщение подтверждения (вы уверены..)
        var confirmMsg = $(this).data('confirm-ajax');

        //Если сообщение есть - запросить подтверждение (окно с сообщением и кнопками "да/нет"
        if(confirmMsg){
            if(!confirm(confirmMsg)){
                return false;
            }
        }

        //Получить контейнер в который нужно загрузить данные
        var container = $($(this).data('ajax-reloader'));

        //Если стоит load-parent - значит загрузка будет осуществляться в родителя данного контейнера
        if($(this).data('load-parent') === 'yes'){
            container = container.parent();
        }

        //Текущий элемент (ссылка)
        var link = $(this);

        //Запрос и загрузка данных
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

