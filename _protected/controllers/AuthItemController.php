<?php
namespace app\controllers;

use app\rbac\models\AuthItem;
use app\rbac\models\AuthItemSearch;
use Yii;
use yii\helpers\Inflector;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * AuthItemController implements the CRUD actions for AuthItem model.
 */
class AuthItemController extends AppController {

  /**
   * Lists all AuthItem models.
   *
   * @return mixed
   */
  public function actionIndex() {
    //$this->addRoute();
    $searchModel = new AuthItemSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  public function actionValidate($id = null) {
    $model = $id === null ? new AuthItem() : AuthItem::findOne(['name' => $id]);
    if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ActiveForm::validate($model);
    }
  }

  public function actionXls() {
    ini_set('memory_limit', '-1');
    $searchModel = new AuthItemSearch();
    $xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
    $xsl_file->send('pack'.date('YmdHis').'.xlsx');
    die;
  }

  private function getPermissionItems($permissions) {
    $authPermissions = ['auth-item-index', 'auth-item-delete', 'auth-item-update', 'auth-item-create', 'auth-item-xls'];
    $permissionsArray = AuthItem::find()->select('name')
                                ->where(['type' => AuthItem::TYPE_PERMISSION])
                                //->andWhere(['not in', 'name', $authPermissions])
                                ->asArray()->column();
    $modelPermissionsArray = [];
    foreach($permissions as $permission) {
      $modelPermissionsArray[] = $permission->name;
    }
    $data = [];
    if(count($permissionsArray) > 0) {
      $controllerNames = [];
      $pathPattern = '\*Controller.php';
      if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        $pathPattern = '/*Controller.php';
      }
      foreach(glob(__DIR__.$pathPattern) as $controller) {
        $controllerName = Inflector::camel2id(preg_replace('/Controller$/', '', basename($controller, '.php')));
        if($controllerName == 'app') {
          continue;
        }
        $controllerNames[] = $controllerName;
      }
      rsort($controllerNames);
      foreach($controllerNames as $controller) {
        $groupItems = [];
        foreach($permissionsArray as $k => $permission) {
          $pos = strpos($permission, $controller);
          if($pos === 0) {
            $groupItems[] = [
              'name' => Yii::t('app', $permission),
              'selected' => in_array($permission, $modelPermissionsArray),
              'value' => $permission
            ];
            unset($permissionsArray[$k]);
          }
        }
        if(count($groupItems) > 0) {
          $data[] = [
            'groupName' => Yii::t('app', $controller),
            'groupData' => $groupItems
          ];
        }
      }
    }
    sort($data);
    return $data;
  }

  /**
   * Creates a new AuthItem model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate($id = null) {

    if(Yii::$app->getRequest()->isAjax) {
      if($id === null) {
        $model = new AuthItem();
        if($model->load(Yii::$app->request->post())) {
          $model->type = AuthItem::TYPE_ROLE;
          if($model->save()) {
            if(isset(Yii::$app->request->post('AuthItem')['permissionList'])) {
              self::setPermissions($model->name, Yii::$app->request->post('AuthItem')['permissionList']);
            }
            $data['status'] = 1;
          } else {
            $data['status'] = 0;
            $data['errors'] = $model->getErrors();
          }
          Yii::$app->response->format = Response::FORMAT_JSON;
          return $data;
        } else {
          $model->permissionList = '';
          $permissionData = self::getPermissionItems($model->permissions);
          return $this->renderAjax('_form', ['model' => $model, 'permissionData' => $permissionData]);
        }
      } else {

        $fromModel = AuthItem::find()->with('permissions')->where(['name' => $id])->one();
        if($fromModel === null) {
          throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $model = new AuthItem();
        if($model->load(Yii::$app->request->post())) {
          $model->type = AuthItem::TYPE_ROLE;
          if($model->save()) {
            if(isset(Yii::$app->request->post('AuthItem')['permissionList'])) {
              self::setPermissions($model->name, Yii::$app->request->post('AuthItem')['permissionList']);
            }
            $data['status'] = 1;
          } else {
            $data['status'] = 0;
            $data['errors'] = $model->getErrors();
          }
          Yii::$app->response->format = Response::FORMAT_JSON;
          return $data;
        } else {
          $list = [];
          foreach($fromModel->permissions as $permission) {
            $list[] = $permission->name;
          }
          $model->permissionList = implode(',', $list);
          $model->name = $fromModel->name."-copy";
          $model->description = 'Copy from «'.$fromModel->name.'»';
          $permissionData = self::getPermissionItems($fromModel->permissions);
          return $this->renderAjax('_form', ['model' => $model, 'permissionData' => $permissionData]);
        }

      }
    } else {
      return $this->redirect(['index']);
    }
  }

  /**
   * Updates an existing AuthItem model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param string $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id) {
    $model = AuthItem::find()->with('permissions')->where(['name' => $id])->one();
    if($model === null) {
      throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
    if(Yii::$app->getRequest()->isAjax) {
      $raw = Yii::$app->request->post('AuthItem');
      if($raw) {
        $model->description = $raw['description'];
        if($model->save()) {
          if(isset($raw['permissionList'])) {
            self::setPermissions($model->name, $raw['permissionList']);
          }
          $data['status'] = 1;
        } else {
          $data['status'] = 0;
          $data['errors'] = $model->getErrors();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
      } else {
        $list = [];
        foreach($model->permissions as $permission) {
          $list[] = $permission->name;
        }
        $model->permissionList = implode(',', $list);
        $permissionData = self::getPermissionItems($model->permissions);
        return $this->renderAjax('_form', ['model' => $model, 'permissionData' => $permissionData]);
      }
    } else {
      return $this->redirect(['index']);
    }
  }

  private function setPermissions($role, $permissions) {
    $table = 'auth_item_child';
    $list = explode(',', $permissions);
    $list = array_unique($list);
    $data = [];
    foreach($list as $item) {
      $data[] = [$role, $item];
    }
    Yii::$app->db->createCommand("DELETE FROM $table WHERE `parent`='$role'")->execute();
    $query = Yii::$app->db->queryBuilder->batchInsert(
      $table,
      ['parent', 'child'],
      $data
    );
    $query = str_replace('INSERT INTO', 'INSERT IGNORE', $query);
    $count = Yii::$app->db->createCommand($query)->execute();
    Yii::$app->authManager->invalidateCache();
    return $count;
  }

  /**
   * Deletes an existing AuthItem model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param string $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id) {
    Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
    $model = AuthItem::find()->where(['name' => $id])->one();
    if($model && $model->delete()) {
      Yii::$app->authManager->invalidateCache();
      return [
        'status' => 1
      ];
    }
    return [
      'status' => 0
    ];
  }

  /**
   * Finds the AuthItem model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param string $id
   *
   * @return AuthItem the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if(($model = AuthItem::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
  private function addRoute()
  {
    $data = [
      'posts-index' => 'posts-index',
      'posts-create' => 'posts-create',
      'posts-update' => 'posts-update',
      'posts-delete' => 'posts-delete',
      'posts-upload' => 'posts-delete',
      'posts-view' => 'posts-delete',
      'posts-delete-image' => 'posts-delete-image',
    ];

    foreach ($data as $key => $val)
    {
      $auth_item = new AuthItem();
      $auth_item->name = $key;
      $auth_item->description = $val;
      $auth_item->type = 2;
      $auth_item->save();
      var_dump($key);


    }
    die();
  }

}
