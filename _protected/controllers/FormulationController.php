<?php

namespace app\controllers;

use Yii;
use app\models\Formulation;
use app\models\FormulationComponent;
use app\models\FormulationSpecification;
use app\models\FormulationBase;
use app\models\FormulationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\db\Query;

/**
 * FormulationController implements the CRUD actions for Formulation model.
 */
class FormulationController extends AppController
{
    /**
     * Lists all Formulation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FormulationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Formulation model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $specificList = FormulationSpecification::find()->where(['formulation_id' => $id])->all();
        $titleList = FormulationComponent::find()->where(['formulation_id' => $id])->all();
        $query = new Query;
        $arrr=[];
        $tt=0;
        $ll=0;
        
        foreach ($titleList as $value) {
            $arrr[$tt] = $value['part_id'];
            $tt++;
        }
       
        $find_code = ($query)->select(["concat(part_no,' ',part_color) as code"])->from('part')->where(["id" => $arrr])->all();
      
        foreach ($titleList as $value) {
            $value->part_id = $find_code[$ll]["code"];
            $ll++;
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
            'specificList'=> $specificList,
            'titleList'=> $titleList
        ]);
    }

    /**
     * Creates a new Formulation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $url_items = '_protected/assets/js/titleList.json';
        $data_items = file_get_contents($url_items);
        $titleList = json_decode($data_items);

        $url_specific = '_protected/assets/js/specificList.json';
        $data_specific = file_get_contents($url_specific);
        $specificList = json_decode($data_specific);
        
        $model = new Formulation();
        $query = new Query;
        $arr = [];
        $t = 0;
        $errorlist = [];
        if ($model->load(Yii::$app->request->post())) {
            foreach ($_POST['items'] as $value) {
                $arr[$t] = $value['code'];
                $t++;
            }
            $find_id = ($query)->select(['id'])->from('part')->where(["concat(part_no,' ',part_color)" => $arr])->all();
               
            $transaction = Yii::$app->db->beginTransaction();
            $query = new Query;
            if ($model->save()) {
                $l=0;
                foreach ($_POST['items'] as $value) {
                    $model_component = new FormulationComponent();
                    if (!isset($value['code'])) {
                        $errorlist['Header'] = Yii::t('app', 'You must fill code field.');
                        return $this->render('create', [
                            'errorlist' => $errorlist ?? null,
                            'model' => $model
                            ,'list'=>self::list()
                            ,'titleList'=>$titleList
                            ,'specificList'=>$specificList
                        ]);
                    } else {
                        $model_component->formulation_id = $model->id;
                        $model_component->part_id = $find_id[$l]["id"];
                        $model_component->std_value = $value['std_value'];
                        $model_component->actual_value = $value['actual_value'];
                        $l++;
                        if ($model_component->save()) {
                            //
                        } else {
                            print_r($model_component->errors);
                        }
                    }
                }

                foreach ($_POST['specs'] as $value) {
                    $model_specification = new FormulationSpecification();
                    if (!isset($value['item'])) {
                        $errorlist['Header'] = Yii::t('app', 'You must fill item field.');
                        return $this->render('create', [
                            'errorlist' => $errorlist ?? null,
                            'model' => $model
                            ,'list'=>self::list()
                            ,'titleList'=>$titleList
                            ,'specificList'=>$specificList
                        ]);
                    } else {
                        $model_specification["formulation_id"] = $model->id;
                        $model_specification["item"] = $value['item'];
                        $model_specification["min"] = $value['min'];
                        $model_specification["max"] = $value['max'];
                        $model_specification["result"] = $value['result'];
                        if ($model_specification->save()) {
                            //
                        } else {
                            print_r($model_specification->errors);
                        }
                    }
                }
                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                $transaction->rollback();
            }
        }

        return $this->render('create', [
            'errorlist' => $errorlist ?? null,
            'model' => $model
            ,'list'=>self::list()
            ,'titleList'=>$titleList
            ,'specificList'=>$specificList
        ]);
    }

    public function list()
    {
        return ArrayHelper::map(FormulationBase::find()->with('part')->all(), 'id', 'basename');
    }

    public function actionPartList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $array=[];
        $out = ['results' => ['id' => '', 'text' => '']];
        $data = ArrayHelper::map(FormulationBase::find()->with('part')->all(), 'id', 'basename');
        foreach ($data as $key => $value) {
            $array[] = ['id' => $key, 'text' => $value];
        }
        $out['results'] = $array;
        return $out;
    }

    
    /**
     * Updates an existing Formulation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $specificList = FormulationSpecification::find()->where(['formulation_id' => $id])->all();
        $titleList = FormulationComponent::find()->where(['formulation_id' => $id])->all();

        $query = new Query;
        $errorlist = [];
        $arr = [];
        $arrr=[];
        $tt=0;
        $ll=0;
        $t=0;
        foreach ($titleList as $value) {
            $arrr[$tt] = $value['part_id'];
            $tt++;
        }
        $find_code = ($query)->select(["concat(part_no,' ',part_color) as code"])->from('part')->where(["id" => $arrr])->all();
        
        foreach ($titleList as $value) {
            $value->part_id = $find_code[$ll]["code"];
            $ll++;
        }
        
        if ($model->load(Yii::$app->request->post())) {
            foreach ($_POST['items'] as $value) {
                $arr[$t] = $value['code'];
                $t++;
            }
            $find_id = ($query)->select(['id'])->from('part')->where(["concat(part_no,' ',part_color)" => $arr])->all();
           
            $transaction = Yii::$app->db->beginTransaction();
            $query = new Query;
            if ($model->save()) {
                $l=0;
                FormulationComponent::deleteAll(['formulation_id' => $id]);
                foreach ($_POST['items'] as $value) {
                    $model_component = new FormulationComponent();
                    if (!isset($value['code'])) {
                        $errorlist['Header'] = Yii::t('app', 'You must fill code field.');
                        return $this->render('create', [
                        'errorlist' => $errorlist ?? null,
                        'model' => $model
                        ,'list'=>self::list()
                        ,'titleList'=>$titleList
                        ,'specificList'=>$specificList
                    ]);
                    } else {
                        $model_component->formulation_id = $model->id;
                        $model_component->part_id = $find_id[$l]["id"];
                        $model_component->std_value = $value['std_value'];
                        $model_component->actual_value = $value['actual_value'];
                        $l++;
                        if ($model_component->save()) {
                            //
                        } else {
                            print_r($model_component->errors);
                        }
                    }
                }
                FormulationSpecification::deleteAll(['formulation_id' => $id]);
                foreach ($_POST['specs'] as $value) {
                    $model_specification = new FormulationSpecification();
                    
                    if (!isset($value['item'])) {
                        $errorlist['Header'] = Yii::t('app', 'You must fill item field.');
                        return $this->render('create', [
                        'errorlist' => $errorlist ?? null,
                        'model' => $model
                        ,'list'=>self::list()
                        ,'titleList'=>$titleList
                        ,'specificList'=>$specificList
                    ]);
                    } else {
                        $model_specification["formulation_id"] = $model->id;
                        $model_specification["item"] = $value['item'];
                        $model_specification["min"] = $value['min'];
                        $model_specification["max"] = $value['max'];
                        $model_specification["result"] = $value['result'];
                        if ($model_specification->save()) {
                            //
                        } else {
                            print_r($model_specification->errors);
                        }
                    }
                }
                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                $transaction->rollback();
            }
        }
        return $this->render('update', [
            'errorlist' => $errorlist ?? null,
            'model' => $model
            ,'list'=>self::list()
            ,'titleList'=>$titleList
            ,'specificList'=>$specificList
        ]);
    }

    /**
     * Deletes an existing Formulation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Formulation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Formulation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Formulation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
