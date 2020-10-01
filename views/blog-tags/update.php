<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BlogTags */

$this->title = 'Update Blog Tags: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Blog Tags', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => \app\components\Utils::encrypt($model->id)]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="blog-tags-update">
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
