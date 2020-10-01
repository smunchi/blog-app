<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\Utils;

/* @var $this yii\web\View */
/* @var $model app\models\BlogCountry */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Blog Countries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$this->registerCss('
.reset-legend { width: inherit; margin-bottom: 10px; border-bottom:none }
.reset-fieldset { border: 1px solid #c0c0c0; padding: 0.35em 0.625em 0.75em; margin: 0 2px }
');
?>
<div class="blog-country-view">

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
                'attribute' => 'continent.name',
                'label' => 'continent'
            ],
            'title',
            'sub_title',
            [
                'attribute' => 'img_src',
                'format' => 'html',
                'label' => 'Image',
                'value' => function($data) {
                    return Html::img($data['img_src']);
                }
            ],
            'content:html',
            'created_at'
        ],
    ]) ?>

    <?php if(count($attractions)) : ?>
    <div class="row">
        <div class="col-md-10">
            <fieldset class="reset-fieldset">
                <legend class="reset-legend">Top attraction of <?php echo $model->name ?></legend>
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Attraction Name</th>
                        <th>Lat</th>
                        <th>Long</th>
                        <th>Image</th>
                    </tr>
                    <?php foreach ($attractions as $attraction) : ?>
                        <tr>
                            <td><?php echo $attraction->name ?></td>
                            <td><?php echo $attraction->lat ?></td>
                            <td><?php echo $attraction->long ?></td>
                            <td><?php echo Html::img($attraction->img_src, ['width' => 60]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </fieldset>
        </div>
    </div>
    <?php endif; ?>
</div>
