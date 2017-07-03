<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\TagModel;

class TagForm extends Model
{

    public $id;
    public $tags;

    public function rules() 
    {
        return [
            ['tags', 'required'],
            ['tags', 'each', 'rule' => ['string']]
        ];
    }

    public function saveTags()
    {
        $ids = [];
        if (!empty($this->tags))
        {
            foreach ($this->tags as $tag) {
                $ids[] = $this->_saveTags($tag);
            }
        }

        return $ids;
    }

    private function _saveTags($tag)
    {
        $model = new TagModel();
        $res = $model->find()->where(['tag_name' => $tag])->one();

        // 保存标签
        if (!$res)
        {
            $model->tag_name = $tag;
            $model->post_num = 1;
            
            if (!$model->save())
            {
                throw new \Exception('保存标签失败');
            }

            return $model->id;
        }
        else 
        {
            $res->updateCounters(['post_num' => 1]);
        }

        return $res->id;
    }
}

?>