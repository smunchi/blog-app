<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\tinymce\TinyMce;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\BlogPost */
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
                url: base_url + "/blog-post/upload-file",
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

$this->registerCss(".kv-file-remove {
    display: none;
}
.selectize-control.form-control {
    border:none;
    padding:0;
}
.form-group.field-blogpost-meta_keywords {
    margin-bottom:-10px;
}
.meta_keywords_detail {
    display:inline-block;
    margin-bottom:15px;
}
");

$previewData = [];
if ($model->featured_image) {
    $previewData[] = $model->featured_image;
}

if (empty($model->author_name)) {
    $model->author_name = \app\models\BlogPost::author_name;
}
?>
<div class="post-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'sub_title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'author_name')->textInput(['maxlength' => true]) ?>
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
            ])->label('Upload Featured Image'); ?>

            <?= $form->field($model, 'existing_upload_image', ['inputOptions' => ['id' => 'existing_upload_image']])->hiddenInput(['value' => $model->featured_image])->label(''); ?>
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
                    'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image flightto hotelto packageto",
                    'branding' => false, // To show or not who powered (TinyMCE label)
                    'external_plugins' => [
                        'flightto' => '/library/js/tinymce.plugin.js',
                        'hotelto' => '/library/js/tinymce.plugin.js',
                        'packageto' => '/library/js/tinymce.plugin.js'
                    ],
                    'resize' => 'both',
                    'file_picker_callback' => new JsExpression('uploadFile')
                ]
            ]); ?>
            <input type='file' name='fileupload' id='fileupload' style='display: none;'>
        </div>
    </div>
    <?php
    echo $this->render('_flight_modal');
    echo $this->render('_hotel_modal');
    echo $this->render('_package_modal');
    ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'type')->dropDownList(['regular' => 'Regular', 'featured' => 'Featured', 'trending' => 'Trending'], ['prompt' => '']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'category_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(\app\models\BlogCategory::find()->all(), 'id', 'name'),
                ['prompt' => '']
            )
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, "tags")->widget(\yii2mod\selectize\Selectize::class, [
                'url' => '/blog-post/search-tags',
                'pluginOptions' => [
                    'plugins' => ['drag_drop', 'remove_button'],
                    'persist' => false,
                    'placeholder' => 'Add tags',
                    'createOnBlur' => true,
                    'valueField' => 'name',
                    'labelField' => 'name',
                    'searchField' => ['name'],
                    'create' => true
                ],
            ]); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'meta_title')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'meta_description')->textarea() ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'meta_keywords')->textInput(['maxlength' => true]) ?>
            <span class="meta_keywords_detail">(Provide multiple keywords separated by comma)</span>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
