<?php
namespace frontend\widgets\hot;

use Yii;
use yii\bootstrap\Widget;
use yii\db\Query;
use common\models\PostExtendModel;
use common\models\PostModel;
use yii\helpers\Url;

class HotWidget extends Widget
{
    // 文章标题
    public $title;

    // 显示条数
    public $limit = 6;

    // 是否显示更多
    public $more = true;

    // 是否显示分页
    public $page = true;
    
    public function run() 
    {
        $res = (new Query())->select('a.browser, b.id, b.title')
            ->from(['a' => PostExtendModel::tableName()])
            ->join('LEFT JOIN', ['b' => PostModel::tableName()], 'a.post_id = b.id')
            ->where('b.is_valid='.PostModel::IS_VALID)
            ->orderBy('a.browser DESC, b.id DESC')
            ->limit($this->limit)
            ->all();
        
        $result['title'] = $this->title;
        $result['more'] = Url::to(['post/index', 'sort' => 'hot']);
        $result['body'] = $res ? : [];

        return $this->render('index', ['data' => $result]);
    }

}


?>