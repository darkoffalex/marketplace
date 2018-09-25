$(document).ready(function () {

    $.fn.extend({
        showHide: function () {
            var $this = $(this);
            if($this.length)
            {
                var conditionsStr = $this.data('activate');
                var conditionsArr = conditionsStr.split(',');

                if(conditionsArr){
                    conditionsArr.forEach(function (item) {
                        var subConditions = item.split(':');
                        var selector = subConditions[0];
                        var value = subConditions[1];

                        if(selector){
                            $(selector).addClass('hidden');
                            $(selector).find('input, select, textarea').attr('disabled', true);
                            if(value === $this.val()){
                                $(selector).removeClass('hidden');
                                $(selector).find('input, select, textarea').attr('disabled', false);
                            }
                        }
                    });
                }
            }
        }
    });

    $('[data-activate]').showHide();
    $(document).on('change', '[data-activate]',function () {
        $(this).showHide();
    });
});
