tinymce.PluginManager.add('flightto', function(editor, url) {
    // Add a button that opens a window
    editor.ui.registry.addButton('flightto', {
        text: 'Add flight',
        onAction: function () {
            // Open window
            $('#flight_to_modal').modal();
        }
    });
});

tinymce.PluginManager.add('hotelto', function(editor, url) {
    // Add a button that opens a window
    editor.ui.registry.addButton('hotelto', {
        text: 'Add hotel',
        onAction: function () {
            // Open window
            $('#hotel_to_modal').modal();
        }
    });
});

tinymce.PluginManager.add('packageto', function(editor, url) {
    // Add a button that opens a window
    editor.ui.registry.addButton('packageto', {
        text: 'Add package',
        onAction: function () {
            // Open window
            $('#package_to_modal').modal();
        }
    });
});