<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BlogBookings */
/* @var $form yii\widgets\ActiveForm */

$previewData = [];
if ($model->img_src) {
    $previewData[] = $model->img_src;
}
?>

<div class="blog-bookings-form">

    <?php $form = ActiveForm::begin(); ?>

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
                    'fileclear' => 'function() { $("#blogbookings-existing_upload_image").val("") }',

                ]
            ]); ?>

            <?= $form->field($model, 'existing_upload_image', ['inputOptions' => ['id' => 'existing_upload_image']])->hiddenInput(['value' => $model->img_src])->label(''); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'button_text')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
