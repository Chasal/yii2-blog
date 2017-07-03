<?php
namespace frontend\widgets\tag;

use Yii;
use yii\bootstrap\Widget;
use common\models\TagModel;

class TagWidget extends Widget
{

    public $title = '';

    public $limnit = 10;

    public function run() 
    {

        $res = TagModel::find()->orderBy(['post_num' => SORT_DESC])->limit($this->limnit)->all();

        $result['title'] = $this->title ? : '标签云';
        $result['body'] = $res? : [];

        return $this->render('index', ['data' => $result]);
    }

}