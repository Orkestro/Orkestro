$(function() {
    $('.chosen-select').chosen();

    var switchElements = document.querySelectorAll('.switchery');
    for (key in switchElements) {
        new Switchery(switchElements[key]);
    }
});