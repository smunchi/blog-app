$(document).ready(function () {
    loadAutocomplete();
    let fileInputData = $('input[id^="input-file"]');
    $.each(fileInputData, function(index, item) {
        enableFileInput($('#'+item.id));
    });

    $(document.body).on('click', '#add-more-attraction', function () {
        let lengthData = $("input[name='attraction[lat][]']").length + 1;
        let element = $('<div class="has-border relative-position">'+
            '<p>'+
            '<input type="text" class="searchTextField" id="'+lengthData+'" name="attraction[name][]" size="25">'+
            '<input type="hidden" id="attraction_lat_'+lengthData+'" name="attraction[lat][]"/>'+
            '<input type="hidden" id="attraction_lng_'+lengthData+'" name="attraction[lng][]"/>'+
            '<a class="close-icon" href="javascript:void(0)"><i class="fa fa-close bg-green"></i></a>'+
            '</p>'+
            '<p>'+
            '<label class="control-label">Upload location image</label>'+
            '<input id="input-file'+lengthData+'" class="input-file" name="attraction[fileinputs][]" type="file" accept="image/*"/>'+
            '</p>'+
            '</div>');
        enableFileInput(element.find('input'));

        if($('.has-border').length > 0) {
            element.insertAfter($('.has-border:last'));
        } else {
            element.insertBefore('#add-more-attraction');
        }

        loadAutocomplete();
    });

    $(document.body).on('click', '.close-icon', function () {
        $(this).closest('.has-border').remove();
    });
});

function enableFileInput(element)
{
    element.id = element.length;
    element.fileinput({
        uploadAsync: true,
        showUpload: false, // hide upload button
        showCaption: false,
        showRemove: true,
        showUpload: false,
        dropZoneEnabled: false,
        initialPreview: (element.siblings('.existing_attraction_img').val() != undefined) ? [element.siblings('.existing_attraction_img').val()] : [],
        fileActionSettings : {
            showDrag:  false
        },
        initialPreviewAsData: true,
    });
}

function loadAutocomplete()
{
    let inputs = $(".searchTextField");
    $.each(inputs, function(index, item) {
        let autocomplete = new google.maps.places.Autocomplete(item, {
            types: ["geocode"]
        });

        autocomplete.inputId = item.id;
        google.maps.event.addListener(autocomplete, "place_changed", function() {
            var place = autocomplete.getPlace();
            console.log(place.geometry.location.lat());
            $("#" + item.id).siblings("#attraction_lat_"+item.id).attr("value", place.geometry.location.lat())
            $("#" + item.id).siblings("#attraction_lng_"+item.id).attr("value", place.geometry.location.lng());
        });
    });
}