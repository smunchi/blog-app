<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BlogCity */

$this->title = Yii::t('app', 'Create Blog City');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Blog Cities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blog-city-create">
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
