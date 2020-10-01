<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BlogCountry */

$this->title = Yii::t('app', 'Create Blog Country');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Blog Countries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blog-country-create">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $this->render('_form', [
                        'model' => $model,
                        'is_create' => true,
                        'mapData' => []
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
