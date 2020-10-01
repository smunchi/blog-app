<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\tinymce\TinyMce;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\BlogCountry */
/* @var $form yii\widgets\ActiveForm */

$uploadFileJs = 'function uploadFile(callback, value, meta) {
        $("#fileupload").trigger("click");
      
        $("#fileupload").on("change", function () {
            var reader = new FileReader();       
            var formData = new FormData();
            var file = this.files[0];
            formData.append("file", file);

            var fileName = "";        
            $.ajax({
                url: base_url + "/blog-country/upload-file",
                type: "post",
                data: formData,  
                processData: false,
                contentType: false,
                async: false,
                success: function (response) {
                    fileName = response;
                }
            });

            reader.onload = function (e) {            
                callback(fileName);
            };
            reader.readAsDataURL(file);
        });
};';
$this->registerJs($uploadFileJs);

$previewData = [];
if ($model->img_src) {
    $previewData[] = $model->img_src;
}

$this->registerCss(".kv-file-remove {
    display: none;
}
#add-more-attraction { 
    margin-top:10px; 
}
.close-icon {
    margin-left:10px;
    right:0px; top:0; position:absolute
}
.relative-position {
    position:relative;
}
.has-border { 
    border:1px solid #cccccc; 
    padding:10px; 
    margin-top:15px; 
}
.file-preview {
    border:none;
}
.close.fileinput-remove {
    display:none;
}
");

$this->registerJsFile("http://maps.google.com/maps/api/js?v=3&&key=your-key&libraries=places");

$attractionJs = $this->registerJsFile('@web/library/js/attractions.js');
$this->registerJs($attractionJs);
?>

<div class="blog-country-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'continent_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(\app\models\BlogContinent::find()->all(), 'id', 'name'),
                ['prompt' => '']
            )
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'sub_title')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'upload_image')->widget(\kartik\file\FileInput::class, [
                'options' => ['accept' => 'image/*', 'multiple' => false],
                'pluginOptions' => [
                    'showCaption' => false,
                    'showRemove' => true,
                    'showUpload' => false,
                    'dropZoneEnabled' => false,
                    'fileActionSettings' => [
                        'showDrag' => false
                    ],
                    'initialPreviewAsData' => true,
                    'initialPreview' => $previewData,
                    'initialPreviewFileType' => 'image'
                ],
                'pluginEvents' => [
                    'fileclear' => 'function() { $("#existing_upload_image").val("") }',

                ]
            ]); ?>

            <?= $form->field($model, 'existing_upload_image', ['inputOptions' => ['id' => 'existing_upload_image']])->hiddenInput(['value' => $model->img_src])->label(''); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'content')->widget(TinyMce::class, [
                'language' => 'en_GB',
                'clientOptions' => [
                    'plugins' => [
                        "advlist autolink lists link charmap print preview",
                        "searchreplace visualblocks code fullscreen wordcount",
                        "insertdatetime media image table contextmenu paste autoresize",
                        "emoticons"
                    ],
                    'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image",
                    'branding' => false, // To show or not who powered (TinyMCE label)
                    'resize' => 'both',
                    'file_picker_callback' => new JsExpression('uploadFile')
                ]
            ]); ?>
            <input type='file' name='fileupload' id='fileupload' style='display: none;'>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10">
            <p style="border-bottom: 1px solid #cccccc; margin-bottom: 20px; margin-top: 30px">Add top attraction</p>
            <div class="form-group">
                <div class="input-container">
                    <?php if (!empty($mapData) && count($mapData)) : ?>
                        <?php
                        $i = count($mapData);
                        foreach ($mapData as $data): ?>
                        <div class="has-border relative-position">
                            <p>
                                <?= Html::input('text', 'attraction[name][]', $data['name'], ['size' => '25px', 'id'=>"$i", 'class' => 'searchTextField']); ?>
                                <input type='hidden' id="attraction_lat_<?= $i ?>" name='attraction[lat][]' value="<?= $data['lat']?>"/>
                                <input type='hidden' id="attraction_lng_<?= $i ?>" name='attraction[lng][]' value="<?= $data['long']?>"/>
                                <a class="close-icon fa fa-lg" href="javascript:void(0)"><i class="fa fa-close btn-primary"></i></a>
                            </p>
                            <p>
                                <label class="control-label">Upload location image</label>
                                <input id="input-file_<?= $i ?>" class="input-file" name="attraction[fileinputs][]" type="file"/>
                                <input type="hidden" class="existing_attraction_img" name="existing_attraction_img" value="<?= $data['img_src'] ?>"/>
                            </p>
                        </div>
                        <?php
                        $i--;
                        endforeach; ?>
                    <?php endif; ?>
                    <?php if(isset($is_create)) : ?>
                        <div class="has-border relative-position">
                            <p>
                                <?= Html::input('text', "attraction[name][]", '', ['size' => '25px', 'id'=>"1", 'class' => 'searchTextField']); ?>
                                <input type='hidden' id="attraction_lat_1" name='attraction[lat][]' value=''/>
                                <input type='hidden' id="attraction_lng_1" name='attraction[lng][]' value=''/>
                            </p>
                            <p>
                                <label class="control-label">Upload location image</label>
                                <input id="input-file_1" class="input-file" name="attraction[fileinputs][]" type="file"/>
                            </p>
                        </div>
                    <?php endif; ?>
                    <?= Html::input('button', 'add-more', 'Add More', ['class' => 'btn btn-primary', 'id' => 'add-more-attraction']); ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">

        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
