var $collectionHolder;

var $addValueLink = $('<a href="#" class="add-value-link">Add a value</a>');
var $newLinkLi = $('<li></li>').append($addValueLink);

$(function() {
    $collectionHolder = $('#orkestro_bundle_productbundle_characteristic_characteristic_values');

    $collectionHolder.append($newLinkLi);

    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addValueLink.on('click', function (e) {
        e.preventDefault();

        addValueForm($collectionHolder, $newLinkLi);
    });
});

function addValueForm($collectionHolder, $newLinkLi)
{
    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var newForm = prototype.replace(/__name__/g, index);

    $collectionHolder.data('index', index + 1);

    var $newFormLi = $('<li></li>').append(newForm);
    $newLinkLi.before($newFormLi);
}
