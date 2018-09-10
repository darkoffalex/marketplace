/**
 * Вспомогательная функция для добавление параметров к URL
 * @param url
 * @param data
 * @returns {*}
 */
var addParamsToUrl = function(url, data) {
    if (!$.isEmptyObject(data)) {
        url += ( url.indexOf('?') >= 0 ? '&' : '?' ) + $.param(data);
    }
    return url;
};

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

    /**
     * При нажатии на такие ссылки к их URL добавляются параметры формы
     */
    $('[data-add-form-params]').click(function () {
        var href = $(this).data('original-url') ? $(this).data('original-url') : $(this).attr('href');
        var formParams = $($(this).data('add-form-params')).serializeArray();

        var formParamsPrepared = {};
        $.each(formParams,function (index,item) {
            formParamsPrepared[item.name] = item.value;
        });

        var hrefNew = addParamsToUrl(href,formParamsPrepared);
        $(this).attr('href',hrefNew);
        return true;
    });

});

