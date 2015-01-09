$(function() {
    var switchElements = document.querySelectorAll('.switchery');
    for (key in switchElements) {
        new Switchery(switchElements[key], { disabled: true, disabledOpacity: 1 });
    }
});