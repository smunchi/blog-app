<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\Utils;

/* @var $this yii\web\View */
/* @var $model app\models\BlogCategory */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Blog Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="blog-category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => Utils::encrypt($model->id)], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => \app\components\Utils::encrypt($model->id)], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'attribute' => 'img_src',
                'format' => 'html',
                'label' => 'Image',
                'value' => function ($data) {
                    return Html::img($data['img_src']);
                },
            ],
            'created_at'
        ],
    ]) ?>

</div>
