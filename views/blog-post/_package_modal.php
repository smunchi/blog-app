<?php
use yii\web\JsExpression;
use yii\bootstrap\Modal;
use kartik\select2\Select2;
use yii\widgets\Pjax;

$packageToolbarJs = '$(function() {      
    $(\'#addPackageToData\').click(function() {
         var data = $("#package_to_modal option:selected").text();     
         if(data != "Select destination") {
             tinymce.activeEditor.execCommand("mceInsertContent", false, \'&lt;package to="\' + data + \'"/&gt;\');
            $("#package_to_modal").modal("hide");
             $.pjax.reload({container: "#package_to_container", async: false});
         }
    });
    
    $("#close-btn-package").click(function() {
         $.pjax.reload({container: "#package_to_container", async: false});
    });
})';
$this->registerJs($packageToolbarJs);

Modal::begin([
    'header' => '<h4 class="modal-title">Add city</b></h4>',
    'options' => [
        'id' => 'package_to_modal',
        'tabindex' => false, // important for Select2 to work properly
    ],
    'footer' => '<button type="button" id="addPackageToData" class="btn btn-success">Add</button>
                     <button type="button" id="close-btn-package" class="btn btn-default" data-dismiss="modal">Close</button>',
]);
Pjax::begin(['id' => 'package_to_container']);
echo Select2::widget([
    'name'          => 'package_to',
    'id'            => 'package_to',
    'pluginOptions' => [
        'placeholder'        => 'Select destination',
        'minimumInputLength' => 3,
        'ajax'               => [
            'url'      => '',
            'dataType' => 'json',
            'method'   => 'GET',
            'data'     => new JsExpression('function(params) { return { keyword:params.term }; }'),
            'processResults'=> new JsExpression('function (data) {             
                     return {                 
                        results: $.map(data.response, function(obj) {
                            return { id: obj.cityCode, text: obj.name };
                        })
                    };
                }')
        ]
    ]
]);
Pjax::end();
Modal::end();
?>
