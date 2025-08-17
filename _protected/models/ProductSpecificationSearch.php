<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProductSpecification;
use PHPExcel_Style_Alignment;

/**
 * ProductSpecificationSearch represents the model behind the search form of app\models\ProductSpecification.
 */
class ProductSpecificationSearch extends ProductSpecification {
	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			[['id', 'part_id', 'status', 'updated_by', 'updated_at'], 'integer'],
			[['code', 'description', 'part_nm', 'amount'], 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios() {
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
	public function search($params, $mode = '') {
		$query = ProductSpecification::find()
			->joinWith(['updatedBy',
				'part' => function ($query) {
					$query->from(['mainPart' => Part::tableName()]);
				}
			]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'product_specification.id' => $this->id,
			'product_specification.part_id' => $this->part_id,
			'product_specification.status' => 1,
			'product_specification.updated_by' => $this->updated_by,
			'product_specification.updated_at' => $this->updated_at,
			'amount' => $this->amount,
		]);

		$query->andFilterWhere(['like', 'code', $this->code])
			->andFilterWhere(['like', 'part.part_name', $this->part_nm])
			->andFilterWhere(['like', 'product_specification.description', $this->description]);

		if ($mode == 'excel') {
			$query = '
				SELECT 
					ps.id as ps_id,
					ps.code as ps_code,
					ps.description as ps_description,
					mainPart.part_no as ps_part_no,
					mainPart.part_name as ps_part_name,
					mainPart.part_color as ps_part_color,
					CASE 
						WHEN mainPart.state = 0 THEN "Component"
						WHEN mainPart.state = 1 THEN "Semi-finished"
						WHEN mainPart.state = 2 THEN "Product"
					END as ps_part_state,
					CASE 
						WHEN mainPart.status = 1 THEN "Актив"
						ELSE "Не актив"
					END as ps_part_status,
					part.part_color as psi_part_color,
					part.part_no as psi_part_no,
					part.part_name as psi_part_name,
					psi.usage_qty as psi_usage_qty,
					unit.unit_value as psi_unit,
					warehouse.name as psi_warehouse,
					CASE 
						WHEN part.state = 0 THEN "Component"
						WHEN part.state = 1 THEN "Semi-finished"
						WHEN part.state = 2 THEN "Product"
					END as psi_part_state,
					CASE 
						WHEN part.status = 1 THEN "Актив"
						ELSE "Не актив"
					END as psi_part_status,
					user.fullname, 
					FROM_UNIXTIME(ps.updated_at) as updated_at,
					CASE 
						WHEN ps.status = 1 THEN "Актив"
						ELSE "Не актив"
					END as status 
				FROM product_specification ps 
				LEFT JOIN user ON ps.updated_by = user.id 
				LEFT JOIN part mainPart ON ps.part_id = mainPart.id 
				LEFT JOIN product_specification_item psi ON ps.id = psi.product_specification_id 
				LEFT JOIN part ON psi.part_id = part.id 
				LEFT JOIN unit ON part.unit_id = unit.id 
				LEFT JOIN warehouse ON psi.warehouse_id = warehouse.id ';
				
			$params = [];
			$where = [];

			if($this->part_id) {
				$params[':ps_part_id']=$this->part_id;
				$where[] = 'ps.part_id=:ps_part_id'; 
			}

			if($this->part_id) {
				$params[':ps_part_id']=$this->part_id;
				$where[] = 'ps.part_id=:ps_part_id'; 
			}

			if($this->status) {
				$params[':ps_status']=$this->status;
				$where[] = 'ps.status=:ps_status'; 
			}

			if($this->code) {
				$params[':ps_code']=$this->code;
				$where[] = "ps.code LIEK '%:ps_code%'"; 
			}
			if(count($where)>0) {
				$query .= 'WHERE '.implode(' AND ', $where);
				$data = Yii::$app->db->createCommand($query, $params)->queryAll();
			} else {
				$data = Yii::$app->db->createCommand($query)->queryAll();
			}

			$ps_id = null;
			$q=0;
			$excel_data=[];
			foreach($data as $index => $record){
				$temp_ps_id = $record['ps_id'];
				if($ps_id == $temp_ps_id){
					$excel_data[$q]['ps_id'] = '';
					$excel_data[$q]['ps_code'] = '';
					$excel_data[$q]['ps_description'] = '';
					$excel_data[$q]['modelname'] = '';
					$excel_data[$q]['ps_part_no'] = '';
					$excel_data[$q]['ps_part_name'] = '';
					$excel_data[$q]['ps_part_color'] = '';
					$excel_data[$q]['ps_part_state'] = '';
					$excel_data[$q]['ps_part_status'] = '';
          $excel_data[$q]['status'] = '';
				} else {
					$excel_data[$q]['ps_id'] = $record['ps_id'];
					$excel_data[$q]['ps_code'] = $record['ps_code'];
					$excel_data[$q]['ps_description'] = $record['ps_description'];
					$excel_data[$q]['modelname'] = $record['modelname'];
					$excel_data[$q]['ps_part_no'] = $record['ps_part_no'];
					$excel_data[$q]['ps_part_name'] = $record['ps_part_name'];
					$excel_data[$q]['ps_part_color'] = $record['ps_part_color'];
					$excel_data[$q]['ps_part_state'] = $record['ps_part_state'];
					$excel_data[$q]['ps_part_status'] = $record['ps_part_status'];
          $excel_data[$q]['status'] = $record['status'];
				}
				
				$excel_data[$q]['psi_part_color'] = $record['psi_part_color'];
				$excel_data[$q]['psi_part_no'] = $record['psi_part_no'];
				$excel_data[$q]['psi_part_name'] = $record['psi_part_name'];
				$excel_data[$q]['psi_usage_qty'] = $record['psi_usage_qty'];
				$excel_data[$q]['psi_unit'] = $record['psi_unit'];
				$excel_data[$q]['psi_warehouse'] = $record['psi_warehouse'];

				$excel_data[$q]['psi_part_state'] = $record['psi_part_state'];
				$excel_data[$q]['psi_part_status'] = $record['psi_part_status'];
				$excel_data[$q]['fullname'] = $record['fullname'];
				$excel_data[$q]['updated_at'] = $record['updated_at'];

				$q++;
				$ps_id = $temp_ps_id;
			}

			$file = Yii::createObject(
				[
					'class' => 'codemix\excelexport\ExcelFile',
					'sheets' => [
						'Parts' => [
							'data' => $excel_data,
							'titles' => [
								Yii::t('app', 'Id'),
								Yii::t('app', 'Code'),
								Yii::t('app', 'Description'),
								Yii::t('app', 'Model'),
								Yii::t('app', 'Part color'),
								Yii::t('app', 'Part No'),
								Yii::t('app', 'Part name'),
								Yii::t('app', 'Part state'),
								Yii::t('app', 'Part status'),
                Yii::t('app', 'Status'),
								Yii::t('app', 'Sub Part color'),
								Yii::t('app', 'Sub Part No'),
								Yii::t('app', 'Sub part name'),
								Yii::t('app', 'Qty'),
								Yii::t('app', 'Unit'),
								Yii::t('app', 'Warehouse'),
								Yii::t('app', 'Sub part state'),
								Yii::t('app', 'Sub part status'),
								Yii::t('app', 'Updated by'),
								Yii::t('app', 'Updated at')
							],
							'styles' => [
								'A1:T1' => [
									'font' => [
										'bold' => true,
										'color' => ['rgb' => '000000'],
									],
									'fill' => [
										'type' => \PHPExcel_Style_Fill::FILL_SOLID,
										'color' => ['rgb' => 'D9E1F2']
									],
									'alignment' => [
										'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
									],
								],
							],
						],
					]
				]
			);

			return $file;
		} else {
			return $dataProvider;
		}
	}
}
