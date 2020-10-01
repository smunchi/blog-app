<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BlogCitySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="blog-city-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'country_id')->dropDownList(
        \yii\helpers\ArrayHelper::map(\app\models\BlogCountry::find()->all(), 'id', 'name'),
        ['prompt' => '']
    )
    ?>
    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'sub_title') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
