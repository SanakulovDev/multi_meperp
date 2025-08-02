<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DocumentSearch represents the model behind the search form of `app\models\Document`.
 */
class DocumentSearch extends Document
{
  /**
   * @inheritdoc
   */
  public function rules()
  {
    return [
      [['id', 'document_type_id', 'from_warehouse_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
      [['docnum', 'series', 'status', 'action', 'filter_from', 'filter_to', 'to_warehouse_id', 'comment', 'serial_number', 'supplier_id'], 'safe'],
    ];
  }

  /**
   * @inheritdoc
   */
  public function scenarios()
  {
    // bypass scenarios() implementation in the parent class
    return Model::scenarios();
  }

  /**
   * Creates data provider instance with search query applied
   *
   * @param array $params
   *
   * @return ActiveDataProvider
   */
  public function search($params, $mode = '')
  {
    $query = Document::find()
    ->with(['toWarehouse','fromWarehouse','fromWarehouse','documentType','supplier']);

    // add conditions that should always apply here

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
    ]);

    $this->load($params);

    if (!$this->validate()) {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
    }

    $arr_to_wh_ids = [];
    if (!empty($this->to_warehouse_id)) {
      $arr_to_wh_ids = explode(',', $this->to_warehouse_id);
    }


    // grid filtering conditions
    $query->andFilterWhere([
      'id' => $this->id,
      'document_type_id' => $this->document_type_id,
      'from_warehouse_id' => $this->from_warehouse_id,
      'to_warehouse_id' => $arr_to_wh_ids,
      'supplier_id' => $this->supplier_id,
      'serial_number' => $this->serial_number,
      'created_by' => $this->created_by,
      'created_at' => $this->created_at,
      'updated_by' => $this->updated_by,
      'updated_at' => $this->updated_at,
    ]);

    if ($this->action === '1') {
      $query->andFilterWhere([
        'to_warehouse_id' => Yii::$app->user->identity->warehouseIds
      ]);
    } elseif ($this->action === '0') {
      $query->andFilterWhere([
        'from_warehouse_id' => Yii::$app->user->identity->warehouseIds
      ]);
    }

    $query->andFilterWhere(['like', 'docnum', $this->docnum])
      ->andFilterWhere(['like', 'series', $this->series])
      ->andFilterWhere(['like', 'comment', $this->comment])
      ->andFilterWhere(['like', 'status', $this->status]);


    if ($mode == 'excel') {

      if (Yii::$app->user->identity->roleName == 'mrp') {
        $query->andWhere([
          'or',
          ['to_warehouse_id' => Yii::$app->user->identity->warehouseIds],
          ['from_warehouse_id' => Yii::$app->user->identity->warehouseIds]
        ]);
      }

      // if(
        
      //   (empty($this->filter_from) || empty($this->filter_to))
        
      //   ){
      //   // Agar Downloadda ikkala sana ham tanlanmas joriy oyni boshidan olaveradi
      //   $filter_from = date('Y-m-01', strtotime('now'));
      //   $filter_to = date('Y-m-t', strtotime('now'));
      //   $query->andWhere(['between', 'docdate', $filter_from, $filter_to]);
      // }else{
      //   $query->andWhere(['between', 'docdate', $this->filter_from, $this->filter_to]);  
      // }

      // echo '<pre>';
      // print_r($params);
      // echo '</pre>';
      // echo '<pre>';
      // print_r($query->createCommand()->rawSql);
      // echo '</pre>';
      // die;

      if (count(array_filter($params ? $params["DocumentSearch"] : [])) == 0) {
        $filter_from = date('Y-m-01', strtotime('now'));
        $filter_to = date('Y-m-t', strtotime('now'));
        $query->andWhere(['between', 'docdate', $filter_from, $filter_to]);
      }else{
        $filter_from = (!empty($this->filter_from)) ? $this->filter_from : '1970-01-01 00:00:00';
        $filter_to = (!empty($this->filter_to)) ? $this->filter_to : '9999-12-31 23:59:59';
        $query->andFilterWhere(['between', 'docdate', $filter_from, $filter_to]);
      }

      //$query->andWhere(['<>', 'to_warehouse_id', Yii::$app->params['deliveryWhId']]);

      $documents = $query->with('documentDetails')->all();
      $arrFile = [];
      foreach ($documents as $document) {
        foreach ($document->documentDetails as $detail) {
          unset($tmpArray);
          $tmpArray['docnum'] = $detail->document->docnum;
          $tmpArray['docdate'] = $detail->document->docdateFormatted;
          $tmpArray['document_type'] = $detail->document->documentType->name;
          $tmpArray['from_warehouse'] = $detail->document->fromWarehouse->name;
          $tmpArray['to_warehouse'] = $detail->document->toWarehouse->name;
          $tmpArray['supplier'] = $detail->document->supplier->name ?? null;
          $tmpArray['status'] = $detail->document->statusName;


          $tmpArray['part_number'] = $detail->part->part_no;
          $tmpArray['part_id'] = $detail->part->part_name;
          $tmpArray['part_color'] = $detail->part->part_color;
          $tmpArray['unit'] = $detail->part->unit->unit_value;
          $tmpArray['qty'] = $detail->qty;

          $tmpArray['created_by'] = $detail->document->createdBy->fullname;
          $tmpArray['created_at'] = $detail->document->createdAtFormatted;
          $tmpArray['updated_by'] = $detail->document->updatedBy->fullname ?? null;
          $tmpArray['updated_at'] = $detail->document->updatedAtFormatted;

          $tmpArray['comment'] = $detail->document->comment;
          $tmpArray['serial_number'] = $detail->document->serial_number;

          $arrFile[] = $tmpArray;
        }
      }
      if (empty($arrFile))
        $query->orderBy(['id' => SORT_DESC]);
      $file = \Yii::createObject([
        'class' => 'codemix\excelexport\ExcelFile',
        'sheets' => [
          'Document' => [
            'data' => $arrFile,
            'titles' => [
              0 => Yii::t('app', 'Document number'),
              1 => Yii::t('app', 'Document date'),
              2 => Yii::t('app', 'Document type'),
              3 => Yii::t('app', 'Warehouse A'),
              4 => Yii::t('app', 'Warehouse B'),
              5 => Yii::t('app', 'Supplier'),
              6 => Yii::t('app', 'Status'),
              8 => Yii::t('app', 'Part number'),
              9 => Yii::t('app', 'Part name'),
              7 => Yii::t('app', 'Part color'),
              10 => Yii::t('app', 'Unit'),
              11 => Yii::t('app', 'Quantity'),
              12 => Yii::t('app', 'Created by'),
              13 => Yii::t('app', 'Created at'),
              14 => Yii::t('app', 'Updated by'),
              15 => Yii::t('app', 'Updated at'),
              16 => Yii::t('app', 'Comment'),
              17 => Yii::t('app', 'Serial number'),


            ],
          ]
        ]
      ]);
      return $file;
    } else {
      $filter_from = (!empty($this->filter_from)) ? $this->filter_from : '1970-01-01 00:00:00';
      $filter_to = (!empty($this->filter_to)) ? $this->filter_to : '9999-12-31 23:59:59';
      $query->andFilterWhere(['between', 'docdate', $filter_from, $filter_to]);

      return $dataProvider;
    }
  }
}
