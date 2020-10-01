<?php

use yii\helpers\Html;
use app\components\Utils;

/* @var $this yii\web\View */
/* @var $model app\models\BlogPost */

$this->title = Yii::t('app', 'Update Post: {name}', [
    'name' => $model->title,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Posts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => Utils::encrypt($model->id)]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="post-update">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
