<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BlogBookings */

$this->title = Yii::t('app', 'Create Blog Bookings');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Blog Bookings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blog-bookings-create">
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
