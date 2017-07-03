<?php

use frontend\widgets\post\PostWidget;
use frontend\widgets\banner\BannerWidget;
use frontend\widgets\chat\ChatWidget;
use frontend\widgets\hot\HotWidget;
use frontend\widgets\tag\TagWidget;


$this->title = '博客';
?>

<div class="row">
    <div class="col-lg-9">
        <?= BannerWidget::widget(); ?>
        
        <?= PostWidget::widget(['limit' => 2, 'page' => true]); ?>
    </div>

    <div class="col-lg-3">
        <?= ChatWidget::widget(); ?>

        <?= HotWidget::widget(); ?>

        <?= TagWidget::widget(); ?>
    </div>

</div>
