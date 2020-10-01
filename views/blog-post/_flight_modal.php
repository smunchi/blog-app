<?php
use yii\web\JsExpression;
use yii\bootstrap\Modal;
use kartik\select2\Select2;
use yii\widgets\Pjax;

$flightToolbarJs = '$(function() {
    $(\'#addFlightToData\').click(function() {
         var data = $("#flight_to_modal option:selected").text();     
         if(data != "Select flight destination") {
            tinymce.activeEditor.execCommand("mceInsertContent", false, \'&lt;flight to="\' + data + \'"/&gt;\');
            $("#flight_to_modal").modal("hide");
            $.pjax.reload({container: "#flight_to_container", async: false});
         }
    });
    
    $("#close-btn-flight").click(function() {
         $.pjax.reload({container: "#flight_to_container", async: false});
    });
})';
$this->registerJs($flightToolbarJs);

Modal::begin([
    'header' => '<h4 class="modal-title">Add flight</b></h4>',
    'options' => [
        'id' => 'flight_to_modal',
        'tabindex' => false, // important for Select2 to work properly
    ],
    'footer' => '<button type="button" id="addFlightToData" class="btn btn-success">Add</button>
                     <button type="button" id="close-btn-flight" class="btn btn-default" data-dismiss="modal">Close</button>',
]);
Pjax::begin(['id' => 'flight_to_container']);
echo Select2::widget([
    'name'          => 'flight_to',
    'value'         => 'DAC',
    'id'            => 'flight_to',
    'pluginOptions' => [
        'placeholder'        => 'Select flight destination',
        'minimumInputLength' => 3,
        'ajax'               => [
            'url'      => '',
            'dataType' => 'json',
            'method'   => 'GET',
            'data'     => new JsExpression('function(params) { return { name:params.term }; }'),
            'processResults'=> new JsExpression('function (data) {             
                     return {
                        results: $.map(data.response, function(obj) {
                            return { id: obj.iata, text: obj.name };
                        })
                    };
                }')
        ]
    ],
]);
Pjax::end();
Modal::end();
?>
