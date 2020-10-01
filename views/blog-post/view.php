<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\Utils;

/* @var $this yii\web\View */
/* @var $model app\models\BlogPost */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Posts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="post-view">

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
    <div class="box">
        <div class="box-body table-responsive">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'title',
                    'slug',
                    'sub_title',
                    [
                        'attribute' => 'featured_image',
                        'format' => 'html',
                        'label' => 'Featured Image',
                        'value' => function ($data) {
                            return Html::img($data['featured_image']);
                        },
                    ],
                    'content:html',
                    'author_name',
                    [
                        'attribute' => 'category.name',
                        'label' => 'Category'
                    ],
                    'views',
                    'type',
                    [
                        'attribute' => 'tags',
                        'label' => 'Tags',
                        'value' => function ($data) {
                            return implode(',', $data->getPostTags()->select('title')->column());
                        },
                    ],
                    'meta_title',
                    'meta_description',
                    'meta_keywords',
                ],
            ]) ?>
        </div>
    </div>

</div>
