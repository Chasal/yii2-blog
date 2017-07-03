<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('common', 'create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Post'), 'url' => ['post/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-9">
        <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'title')->textinput(['maxlength' => true]); ?>  <!-- true的意思是匹配rules对应的规则 -->

            
            <?= $form->field($model, 'cat_id')->dropDownList(
                $cat, ['prompt' => '请选择']
            ); ?>

            <?= $form->field($model, 'label_img')->widget('common\widgets\file_upload\FileUpload',[
                'config'=>[
                    //图片上传的一些配置，不写调用默认配置
                    // 'domain_url' => 'http://www.yii-china.com',
                ]
            ]); ?>

            <?= $form->field($model, 'content')->widget('common\widgets\ueditor\Ueditor',[
                'options'=>[
                    'initialFrameHeight' => 350,
                ]
            ]); ?>

            <?= $form->field($model, 'tags')->widget('common\widgets\tags\TagWidget'); ?>

            <div class="form-group">
                <?= Html::submitButton('发布', ['class' => 'btn btn-primary', 'name' => 'create-button']); ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div>

    <div class="col-lg-3">
        <div>注意事项：</div>
    </div>
</div>
