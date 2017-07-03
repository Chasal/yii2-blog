<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel common\models\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '内容管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-model-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title' => [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function ($model) 
                {
                    return '<a href="http://frontend.hyii2.com/'.Url::to(['post/view', 'id'=> $model->id]).'">'.$model->title.'</a>';
                }
            ],
            // 'summary',
            // 'content:ntext',
            // 'label_img',
            'cat_id' => [  // OR 'cat.cat_name', 字体为黑色
                'attribute' => 'cat_id',
                'value' => function ($model)
                {
                    return $model->cat->cat_name;
                }
            ],
            // 'user_id',
            'user_name',
            'is_valid' => [
                'attribute' => 'is_valid',
                'value' => function ($model) 
                {
                    return $model->is_valid == 0 ? '无效' : '有效';
                },
                'filter' => [
                    '0' => '无效',
                    '1' => '有效'
                ]
            ],
            'created_at:datetime',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
