<?php
use yii\web\JsExpression;
use yii\bootstrap\Modal;
use kartik\select2\Select2;
use yii\widgets\Pjax;

$hotelToolbarJs = '$(function() {      
    $(\'#addHotelToData\').click(function() {
         var data = $("#hotel_to_modal option:selected").text();     
         if(data != "Select hotel or city destination") {
             tinymce.activeEditor.execCommand("mceInsertContent", false, \'&lt;hotel to="\' + data + \'"/&gt;\');
             $("#hotel_to_modal").modal("hide");
             $.pjax.reload({container: "#hotel_to_container", async: false});
         }
    });
    
    $("#close-btn-hotel").click(function() {
         $.pjax.reload({container: "#hotel_to_container", async: false});
    });
})';
$this->registerJs($hotelToolbarJs);

Modal::begin([
    'header' => '<h4 class="modal-title">Add hotel or city</b></h4>',
    'options' => [
        'id' => 'hotel_to_modal',
        'tabindex' => false, // important for Select2 to work properly
    ],
    'footer' => '<button type="button" id="addHotelToData" class="btn btn-success">Add</button>
                     <button type="button" id="close-btn-hotel" class="btn btn-default" data-dismiss="modal">Close</button>',
]);
Pjax::begin(['id' => 'hotel_to_container']);
echo Select2::widget([
    'name'          => 'hotel_to',
    'id'            => 'hotel_to',
    'pluginOptions' => [
        'placeholder'        => 'Select hotel or city destination',
        'minimumInputLength' => 3,
        'ajax'               => [
            'url'      => '',
            'dataType' => 'json',
            'method'   => 'GET',
            'data'     => new JsExpression('function(params) { return { keyword:params.term }; }'),
            'processResults'=> new JsExpression('function (data) {             
                     return {                 
                        results: $.map(data.response.hotel, function(obj) {
                            return { id: obj.id, text: obj._name };
                        }).concat($.map(data.response.city, function(obj) {
                            return { id: obj.id, text: obj.name };
                        }))
                    };
                }')
        ]
    ]
]);
Pjax::end();
Modal::end();
?>
