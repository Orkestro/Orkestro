var $collectionHolder;

var $addCharacteristicLink = $('<a href="#" class="add-value-link">Add a characteristic</a>');
var $newLinkLi = $('<li></li>').append($addCharacteristicLink);

$(function() {
    $collectionHolder = $('#orkestro_bundle_productbundle_productkind_characteristics');

    $collectionHolder.append($newLinkLi);

    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addCharacteristicLink.on('click', function (e) {
        e.preventDefault();

        addCharacteristicForm($collectionHolder, $newLinkLi);
    });
});

function addCharacteristicForm($collectionHolder, $newLinkLi)
{
    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var newForm = prototype.replace(/__name__/g, index);

    $collectionHolder.data('index', index + 1);

    var $newFormLi = $('<li></li>').append(newForm);
    $newLinkLi.before($newFormLi);
}
