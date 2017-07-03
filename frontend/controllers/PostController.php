<?php
namespace frontend\controllers;

use Yii;
use frontend\controllers\base\BaseController;
use frontend\models\PostForm;
use common\models\CatModel;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\PostExtendModel;

class PostController extends BaseController
{   
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'upload', 'ueditor'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['create', 'upload', 'ueditor'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    '*' => ['get', 'post']
                    //'logout' => ['post'],
                ],
            ],
        ];
    }

    // 把一些扩展类通过调用放到upload()里面，相当于actionUpload()
    public function actions()
    {
        return [
            'upload' => [
                'class' => 'common\widgets\file_upload\UploadAction',
                'config' => [
                    'imagePathFormat' => "/image/{yyyy}{mm}{dd}/{time}{rand:6}",
                ]
            ],
            'ueditor'=>[
                'class' => 'common\widgets\ueditor\UeditorAction',
                'config'=>[
                    //上传图片配置
                    'imageUrlPrefix' => "", /* 图片访问路径前缀 */
                    'imagePathFormat' => "/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
            ]
        ]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    // 文章创建
    public function actionCreate()
    {
        $model = new PostForm;

        // 定义场景
        $model->setScenario(PostForm::SCENARIOS_CREATE);

        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            if (!$model->create())
            {
                Yii::$app->session->setFlash('warning', $model->_lastError);
            } 
            else 
            {
                return $this->redirect(['post/view', 'id' => $model->id]);
            }
        }

        $cat = CatModel::getAllCats();
        return $this->render('create', ['model' => $model, 'cat' => $cat]);
    }

    // 文章详情
    public function actionView($id)
    {
        $model = new PostForm();
        $data = $model->getViewById($id);

        // 文章统计
        $model = new PostExtendModel();
        $model->upCounter(['post_id' => $id], 'browser', 1);
        return $this->render('view', ['data' => $data]);
    }
}

?>