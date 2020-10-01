<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use app\components\Utils;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Blog Continents');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blog-continent-index">

    <?php Pjax::begin(); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Blog Continent'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'created_at',
            [
                'class' => 'kartik\grid\ActionColumn',
                'contentOptions' => ['style' => 'width: 120px'],
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => Utils::encrypt($model->id)], [
                            'title' => Yii::t('app', 'Details'),
                            'class' => 'btn btn-default btn-xs custom_button',
                            'data-pjax' => '0',
                        ]);
                    },
                    'update' => function ($url, $model, $key) {

                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => Utils::encrypt($model->id)], [
                            'title' => Yii::t('app', 'Edit'),
                            'class' => 'btn btn-default btn-xs custom_button',
                            'data-pjax' => '0',
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => Utils::encrypt($model->id)], [
                            'title' => Yii::t('app', 'Delete'),
                            'class' => 'btn btn-default btn-xs custom_button',
                            'data-pjax' => '0',
                        ]);
                    }
                ],
            ]
        ],
        'containerOptions' => ['style' => 'overflow: auto'],
        'pjax' => true,
        'bordered' => true,
        'striped' => true,
        'condensed' => false,
        'responsive' => false,
        'hover' => true,
        'showPageSummary' => false,
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => $this->title
        ]
    ]); ?>
    <?php Pjax::end(); ?>
</div>
