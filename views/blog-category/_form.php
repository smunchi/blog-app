<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BlogCategory */
/* @var $form yii\widgets\ActiveForm */

$previewData = [];
if($model->img_src) {
    $previewData[] = $model->img_src;
}
?>

<div class="blog-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
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

    <?= $form->field($model, 'existing_upload_image', ['inputOptions' => ['id' => 'existing_upload_image']])->hiddenInput(['value'=>$model->img_src])->label(''); ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
