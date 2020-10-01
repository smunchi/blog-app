<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\Utils;

/* @var $this yii\web\View */
/* @var $model app\models\BlogBookings */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Blog Bookings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="blog-bookings-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => Utils::encrypt($model->id)], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => Utils::encrypt($model->id)], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'title',
            'sub_title',
            [
                'attribute' => 'img_src',
                'format' => 'html',
                'label' => 'Image',
                'value' => function ($data) {
                    return Html::img($data['img_src']);
                },
            ],
            'button_text',
            'link',
        ],
    ]) ?>

</div>
