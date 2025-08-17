<?php

namespace app\controllers;

use app\models\Machine;
use app\models\MachineSearch;
use app\models\Mold;
use app\models\MoldMachine;
use app\models\MoldPart;
use app\models\ProductionOrder;
use app\models\ProductLine;
use app\models\User;
use Yii;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * MachineController implements the CRUD actions for Machine model.
 */
class MachineController extends AppController
{

  /**
   * Lists all Machine models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new MachineSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return $this->render('index', array_merge(
      [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
      ],
      self::loadDictionaries()
    ));
  }


  /**
   * Creates a new Machine model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new Machine();

    if ($model->load(Yii::$app->request->post())) {

      $transaction = Yii::$app->db->beginTransaction();
      $model->created_by = Yii::$app->user->id;
      $model->created_at = time();
      if ($model->save()) {
        $l = 0;
        foreach ($_POST['moldName'] as $value) {
          $mold_machine = new MoldMachine();
          $mold_machine->created_by = Yii::$app->user->id;
          $mold_machine->created_at = time();
          if (!isset($value)) {
            $errorlist['Header'] = Yii::t('app', 'You must fill empty fields.');
            return $this->renderAjax('_form', array_merge([
              'errorlist' => $errorlist ?? null,
              'model' => $model,
            ]));
          } else {
            $mold_machine->machine_id = $model->id;
            $mold_machine->mold_id = $value;
            $l++;
            if ($mold_machine->save()) {
              //
            } else {
              print_r($mold_machine->errors);
            }
          }
        }
        $transaction->commit();
        return $this->redirect(['index']);
      } else {
        $transaction->rollback();
      }
    }

    $arr = [];
    return $this->renderAjax('_form', array_merge([
      'model' => $model,
      'arr' => $arr
    ]));
  }

  /**
   * Updates an existing Machine model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param int $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    // $model = $this->findModel($id);
    $model = Machine::find()->with(['createdBy',
      'updatedBy' => function($query){
        $query->from(['u2' => User::tableName()]);
      }
    ])->where(['id'=>$id])->one();
    if(!$model) throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));

    $mold_machine = MoldMachine::find()->select('mold_id')->where(['machine_id' => $id])->all();
    $query = new Query;
    $arr = [];
    $t = 0;
    foreach ($mold_machine as $value) {
      $arr[$t] = $value['mold_id'];
      $t++;
    }



    if ($model->load(Yii::$app->request->post())) {

      $transaction = Yii::$app->db->beginTransaction();
      $model->created_by = Yii::$app->user->id;
      $model->created_at = time();
      if ($model->save()) {
        $l = 0;
        MoldMachine::deleteAll(['machine_id' => $id]);
        foreach ($_POST['moldName'] as $value) {
          $mold_machine = new MoldMachine();
          $mold_machine->created_by = Yii::$app->user->id;
          $mold_machine->created_at = time();
          if (!isset($value)) {
            $errorlist['Header'] = Yii::t('app', 'You must fill empty fields.');
            return $this->renderAjax('_form', array_merge([
              'errorlist' => $errorlist ?? null,
              'model' => $model,
            ]));
          } else {
            $mold_machine->machine_id = $model->id;
            $mold_machine->mold_id = $value;
            $l++;
            if ($mold_machine->save()) {
              //
            } else {
              print_r($mold_machine->errors);
            }
          }
        }
        $transaction->commit();
        return $this->redirect(['index']);
      } else {
        $transaction->rollback();
      }
    }

    return $this->renderAjax('_form', array_merge([
      'model' => $model,
      'arr' => $arr
    ]));
  }

  /**
   * Deletes an existing Machine model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param int $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id)
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $model = $this->findModel($id);
    if($model){
      MoldMachine::deleteAll(['machine_id'=>$id]);
      if($model->delete()){
        return [
          "status" => 1
        ];
      }
    }
    return [
      "status" => 0
    ];
  }

  /**
   * Finds the Machine model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param int $id
   * @return Machine the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = Machine::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  private function loadDictionaries()
  {
    $zones = ArrayHelper::map(ProductLine::find()->where('is_zone = 1')->all(), 'id', 'linename');
    $molds = ArrayHelper::map(Mold::find()->all(), 'id', 'mold_no');
    return compact('zones','molds');
  }

  public function actionCounter()
  {
    $this->layout = 'req';
    $zone_list = ProductLine::find()->select('id,linename,description')
      ->where(['is_zone' => 1])->asArray()->all() ?? null;
    return $this->render('counter', [
      'zone_list' => $zone_list,
    ]);
  }



  public function actionMachineList()
  {
    $out = '';
    $this_id = $_POST['this_id'];
    // Zonaning DEVICE lari ruyxati
    if (substr($this_id, 0, 1) == 'z') {
      $machines = Machine::find()
        ->where("status=1 and mold_id >= 1 and product_line_id=" . substr($this_id, 1))
        ->orderBy('sequence')->all() ?? null;
      $machine_list = [];
      $cnt = 0;
      foreach ($machines as $machine) {
        $cnt++;
        $c_sts = (date("Ymd H", $machine->updated_at) == date("Ymd H", strtotime("now"))) ? "bg-green-active" : "bg-red-active";
        $machine_list['machine'][] = [
          'machine_id' => $machine->id,
          'machine_no' => $machine->no,
          'title' => $machine->title,
          'last_count' => $machine->last_count,
          'mold_id' => $machine->mold_id,
          'mold_no' => $machine->mold->mold_no,
          'part_no' => $machine->mold->part_number,
          'sts' => $c_sts,
        ];
      }
      $machine_list['sms404'] = (count($machine_list) == 0) ? Yii::t('app', 'Active machine not found') : null;
      $machine_list['cnt'] = $cnt;
      $machine_list['m_type'] = 'z';
      $out = $machine_list;
    }

    // DEVICEning Partlari va QTY
    if (substr($this_id, 0, 1) == 'm') {
      $parts = MoldPart::find()
        ->where("mold_id=" . substr($this_id, 1))
        ->orderBy('part_id')
        ->all() ?? null;
      $part_list = [];
      $cnt = 0;
      foreach ($parts as $part) {
        $cnt++;
        $part_list['part_data'][] = [
          'part_id' => $part->part_id,
          'part_no' => $part->part->part_no . " " . $part->part->part_color,
          'part_qty' => $part->quantity,
        ];
      }
      $part_list['sms404'] = (count($part_list) == 0) ? Yii::t('app', 'Active mold not found') : null;
      $part_list['cnt'] = $cnt;
      $part_list['m_type'] = 'm';
      $out = $part_list;
    }
    Yii::$app->response->format = Response::FORMAT_JSON;
    return $out;
  }

    public function actionMachineMoldList(){
        $out = '';
        $machine_id = $_REQUEST['machine_id'];
        // DEVICEning MOLDlari va Uning Part+QTYlari
        $mold_machines = MoldMachine::find()
                ->joinWith(["partLists"])
                ->where("machine_id=" . $machine_id)
                ->orderBy('mold_id')
            ->all() ?? null;
        $mold_list = [];
        $cnt = 0;
        foreach($mold_machines as $mold_machine) {
            $cnt++;
            $part_list='';
            foreach($mold_machine->moldParts as $mold_parts){
                $part_list .="<br>".$mold_parts->part->part_no." ".$mold_parts->part->part_color.
                "<sup> <span class='label label-danger' style='font-size: 90%'>".$mold_parts->quantity."</span> </sup>";
            }
	        $part_list= ltrim($part_list,"<br>");
            $mold_list['mold_data'][] = [
                'mold_id' => $mold_machine->mold_id,
                'mold_no' => $mold_machine->mold->mold_no . "(" . $mold_machine->mold->part_number. ") - " . $mold_machine->mold->part_name,
                'part_list' => $part_list,
            ];
        }
        $mold_list['cnt'] = $cnt;
        $mold_list['sms404'] =(count($mold_list)==0)? Yii::t('app', 'Active mold not found'):null;

//        echo "<pre>1: "; print_r($mold_list);echo "</pre>";
//        die;

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $mold_list;
    }

	public function actionMachineSetting(){
		Yii::$app->response->format = Response::FORMAT_JSON;
		$post = $_POST;
		$machine_model = Machine::findOne($post['machine_id']);
		$machine_model->last_count = 0;
		$machine_model->mold_id = $post['mold_id'];
		$machine_model->updated_by = Yii::$app->user->identity->id;
		$machine_model->updated_at = time();
		$transaction = Yii::$app->db->beginTransaction();

		if(!$machine_model->save()){
			$message = Yii::t('app', 'Machine not updated. Something is wrong.');
			$transaction->rollBack();
			$errors = implode(PHP_EOL, $machine_model->errors);
			$result = ['sts' => 'ERROR', 'sms' => $message . PHP_EOL . $errors];
			return $result;
		}
		$transaction->commit();
		$message = Yii::t('app', 'Done ✓');
		return ['sts' => 'OK', 'sms' => $message];
	}
}
