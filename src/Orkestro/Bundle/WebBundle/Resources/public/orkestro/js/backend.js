$(function() {
    $('input[name="flash-notification"]').each(function() {
        $.niftyNoty({
            type: $(this).data('type'),
            container: 'floating',
            html: $(this).data('contents'),
            timer: 3000
        });
    });

    $('.chosen').chosen();
    $('.selectpicker').selectpicker();

    var switchElements = document.querySelectorAll('.switchery');

    for (key in switchElements) {
        var switchElement = switchElements[key];
        if ('object' == typeof(switchElement)) {
            var $switchElement = $(switchElement);
            new Switchery(switchElement, $switchElement.data());

            if ($switchElement.hasClass('submittable')) {
                $switchElement.change(function() {
                    $(this).parents('form').submit();
                });
            }
        }
    }

    $('select#orkestro_bundle_webbundle_backend_pagination_limit_selector_limit').change(function() {
        $(this).parents('form').submit();
    });
});
