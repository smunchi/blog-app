<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use app\components\Utils;
/* @var $this yii\web\View */
/* @var $searchModel app\models\BlogPostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Posts');
$this->params['breadcrumbs'][] = $this->title;

$model = new \app\models\BlogPost();

Modal::begin([
    'header' => '<h4 style="margin:0; padding:0">Search</h4>',
    'id' => 'filter-search',
    'size'=>'modal-medium',
    'options' => [
        'id' => 'filter',
        'tabindex' => false // important for Select2 to work properly
    ],
]);

echo $this->render('_search', ['model'=>$searchModel]);

Modal::end();
?>
<div class="post-index">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Post'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            'title',
            'author_name',
            [
                'attribute' => 'category.name',
                'label' => 'Category'
            ],
            'type',
            'created_at',
            [
                'class' => 'kartik\grid\ActionColumn',
                'contentOptions' => ['style' => 'width: 120px'],
                'template' => '{view} {update} {delete}',
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
                            'format' => 'raw',
                            'class' => 'btn btn-default btn-xs custom_button',
                            'data-pjax' => '0',
                            'data-method' => 'post',
                            'data-confirm' => 'Are you sure you want to delete?'
                        ]);
                    }
                ],
            ],
        ],
        'containerOptions' => ['style' => 'overflow: auto'],
        'toolbar' => [
            ['content' =>
                Html::button('<i class="glyphicon glyphicon-filter"></i>', [
                    'type' => 'button',
                    'data-toggle' => 'modal',
                    'data-target' => '#filter',
                    'title' => Yii::t('app', 'Filter'),
                    'class' => 'btn btn-default'
                ]) . ' ' .
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('app', 'Reset Grid')])
            ],
            '{export}',
            '{toggleData}'
        ],
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
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
