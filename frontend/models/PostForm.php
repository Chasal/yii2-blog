<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\PostModel;
use common\models\RelationPostTagModel;
use yii\db\Query;

class PostForm extends Model
{
    public $id;
    public $title;
    public $content;
    public $label_img;
    public $cat_id;
    public $tags;

    public $_lastError = "";

    /**
     * 定义场景
     * SCENARIOS_CREATE 创建
     * SCENARIOS_UPDATE 更新
     */
    const SCENARIOS_CREATE = 'create';
    const SCENARIOS_UPDATE = 'update';

    /**
     * 定义事件
     * EVENT_AFTER_CREATE 创建
     * EVENT_AFTER_UPDATE 更新
     */
    const EVENT_AFTER_CREATE = 'eventAfterCreate';
    const EVENT_AFTER_UPDATE = 'eventAfterUpdate';


    // 场景设置，限制更改的属性
    public function scenarios()
    {
        $scenarios = [
            self::SCENARIOS_CREATE => ['title', 'content', 'label_img', 'cat_id', 'tags'],
            self::SCENARIOS_UPDATE => ['title', 'content', 'label_img', 'cat_id', 'tags']
        ];

        return array_merge(parent::scenarios(), $scenarios); //覆盖原先的场景设置
    }

    public function rules()
    {
        return [
            [['id', 'title', 'content'], 'required'],
            [['id', 'cat_id'], 'integer'],
            ['title', 'string', 'min' => 2, 'max' => 50]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'PostId'),
            'title' => Yii::t('common', 'PostTitle'),
            'content' => Yii::t('common', 'PostContent'),
            'label_img' => Yii::t('common', 'PostLabelImg'),
            'cat_id' => Yii::t('common', 'CatId'),
            'tags' => Yii::t('common', 'PostTags'),
        ];
    }

    public function create()
    {
        //事务
        $transaction = Yii::$app->db->beginTransaction();

        try
        {
            $model = new PostModel;
            $model->setAttributes($this->attributes); // 把当前有的数据设置到新new出来的PostModel中
            $model->summary = $this->_getSummary();
            $model->user_id = Yii::$app->user->identity->id;
            $model->user_name = Yii::$app->user->identity->username;
            $model->is_valid = PostModel::NO_VALID;
            $model->created_at = time();
            $model->updated_at = time();

            if (!$model->save())
            {
                throw new \Exception('文章保存失败！');
            }

            $this->id = $model->id;

            // 调用事件
            $data = array_merge($this->getAttributes(), $model->getAttributes());
            $this->_eventAfterCreate($data);

            $transaction->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $transaction->rollBack();
            $this->_lastError = $e->getMessage();
            return false;
        }
    }

    //截取文章摘要
    private function _getSummary($s = 0, $e = 90, $char = 'utf-8')
    {
        if (empty($this->content))
        {
            return null;
        }

        return (mb_substr(str_replace('&nbsp;', '', strip_tags($this->content)), $s, $e, $char));
    }

    // 文章创建之后的事件
    private function _eventAfterCreate($data)
    {   
        //添加事件，如果有多个，则写多个
        $this->on(self::EVENT_AFTER_CREATE, [$this, '_eventAddTag'], $data);

        // 触发事件
        $this->trigger(self::EVENT_AFTER_CREATE);
    }

    // 添加标签
    public function _eventAddTag($event)
    {
        // 保存标签
        $tag = new TagForm();
        $tag->tags = $event->data['tags'];
        $tagids = $tag->saveTags();

        // 删除原先的关联关系
        RelationPostTagModel::deleteAll(['post_id' => $event->data['id']]);

        // 批量保存文章和标签的关联关系
        if (!empty($tagids))
        {
            foreach ($tagids as $k => $id) {
                $row[$k]['post_id'] = $this->id;
                $row[$k]['tag_id'] = $id;
            }

            $res = (new Query())->createCommand()->batchInsert(RelationPostTagModel::tableName(), ['post_id', 'tag_id'], $row)->execute();

            if (!$res) {
                throw new \Exception('关联关系保存失败');
            }
        }
    }

     
    public function getViewById($id)
    {
        $res = PostModel::find()->with('relate.tag', 'extend')->where(['id' => $id])->asArray()->one();

        if (!$res)
        {
            throw new \yii\web\NotFoundHttpException('文章不存在');
        }

        // 处理标签格式
        $res['tags'] = [];

        if (isset($res['relate']) && !empty($res['relate']))
        {
            foreach ($res['relate'] as $list) {
                $res['tags'][] = $list['tag']['tag_name'];
            }
        }

        unset($res['relate']);
        return $res;
    }

    public static function getList($cond, $curPage, $pageSize = 5, $orderBy = ['id' => SORT_DESC])
    {
        $model = new PostModel();

        // 查询语句
        $select = ['id', 'title', 'summary', 'label_img', 'cat_id', 'user_id', 'user_name', 'is_valid', 'created_at', 'updated_at'];
        
        $query = $model->find()->select($select)->where($cond)->with('relate.tag', 'extend')->orderBy($orderBy);
        
        // 获取分页数据
        $res = $model->getPages($query, $curPage, $pageSize);

        // 格式化
        $res['data'] = self::_formatList($res['data']);

        return $res;
    }

    // 数据格式化
    public static function _formatList($data)
    {   
        // & 数组的引用，还是原数组，不是数组的拷贝
        foreach ($data as &$list) {
            $list['tags'] = [];
            if (isset($list['relate']) && !empty($list['relate']))
            {
                foreach ($list['relate'] as $lt) 
                {
                    $list['tags'][] = $lt['tag']['tag_name'];
                }
            }
    
            unset($list['relate']);
        }
        return $data;
    }
}

?>  