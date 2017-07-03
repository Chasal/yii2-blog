<?php
namespace frontend\controllers\base;

use Yii;
use yii\web\Controller;

class BaseController extends Controller {

  public function beforeAction($action) {

    if (!parent::beforeAction($action)) {
      return false;
    } else {
      return true;
    }
  }

}

?>