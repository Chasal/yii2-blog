<?php
namespace frontend\widgets\chat;

use Yii;
use yii\bootstrap\Widget;
use frontend\models\FeedForm;

class ChatWidget extends Widget
{

    public function run ()
    {

        $model = new FeedForm;
        $data['feed'] = $model->getList();
        // print_r($data); exit;
        return $this->render('index', ['data' => $data]);
    }

}

?>