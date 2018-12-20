<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Blog */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Blogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blog-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            'id',
            'title',
            'category_id',
            [
                'attribute' => 'content',
                'format' => 'raw'
            ],
            [
                'attribute' => 'create_at',
                'value' => $model->create_at == 0 ? 0 : date('Y-m-d H:i', $model->create_at),
            ],
            [
                'attribute' => 'update_at',
                'value' => $model->update_at == 0 ? 0 : date('Y-m-d H:i', $model->update_at),
            ],
            [
                'attribute' => 'publish_at',
                'value' => $model->publish_at == 0 ? 0 : date('Y-m-d H:i', $model->publish_at),
            ],
            'tag',
            'display',
        ],
    ]) ?>

</div>
