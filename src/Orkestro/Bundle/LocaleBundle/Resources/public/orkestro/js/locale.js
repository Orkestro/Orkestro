$(function() {
    $('.switchery').change(function() {
        $(this).parents('form').submit();
    });
});