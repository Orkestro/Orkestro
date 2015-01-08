$(function() {
    $('.switchery').change(function() {
        $(this).parents('form').submit();
    });

    var switchElements = document.querySelectorAll('.switchery');
    for (key in switchElements) {
        new Switchery(switchElements[key]);
    }
});