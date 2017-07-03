<?php

use frontend\widgets\post\PostWidget;
use yii\helpers\Url;

$this->title = Yii::t('common', 'Post');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-9">
        <?= PostWidget::widget(['limit' => 2, 'page' => true]) ?>
    </div>

    <div class="col-lg-3">
        <a href="<?= Url::to('/post/create') ?>" class="btn btn-success btn-block btn-post">创建文章</a>
    </div>
</div>
