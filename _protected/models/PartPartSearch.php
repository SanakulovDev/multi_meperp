<?php
	namespace app\models;

	use PHPExcel_Style_Alignment;
	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;
	use const yii\db\Query;

	/**
	 * PartPartSearch represents the model behind the search form of `app\models\PartPart`.
	 */
	class PartPartSearch extends PartPart{

		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['id', 'part_id', 'sub_part_id', 'status', 'warehouse_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['usage_qty'], 'number'],
				[['remark', 'unit_value', 'part_nm', 'sub_part_nm', 'part_color'], 'safe'],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function scenarios(){
			// bypass scenarios() implementation in the parent class
			return Model::scenarios();
		}

		/**
		 * Creates data provider instance with search query applied
		 * @param array $params
		 * @return ActiveDataProvider
		 */
		public function search($params, $mode = ''){
			$query = PartPart::find()->joinWith(['subPart.unit', 'warehouse', 'createdBy',
			                                     'part' => function($query){
				                                     $query->from(['mainPart' => Part::tableName()]);
			                                     },
			                                     'updatedBy' => function($query){
				                                     $query->from(['u2' => User::tableName()]);
			                                     }
			                                    ]);
                                          
			// add conditions that should always apply here
			$dataProvider = new ActiveDataProvider(['query' => $query,]);
			$this->load($params);
			if(!$this->validate()){
				// uncomment the following line if you do not want to return any records when validation fails
				// $query->where('0=1');
				return $dataProvider;
			}
			// grid filtering conditions
			$query->andFilterWhere([
				                       'part_part.id' => $this->id,
				                       'part_id' => $this->part_id,
				                       'sub_part_id' => $this->sub_part_id,
				                       'usage_qty' => $this->usage_qty,
				                       'part_part.warehouse_id' => $this->warehouse_id,
				                       'part_part.status' => $this->status,
				                       'part_part.created_by' => $this->created_by,
				                       'part_part.created_at' => $this->created_at,
				                       'part_part.updated_by' => $this->updated_by,
				                       'part_part.updated_at' => $this->updated_at,
			                       ]);
			$query->andFilterWhere(['like', 'part_part.remark', $this->remark])
			      ->andFilterWhere(['like', 'unit_value', $this->unit_value])
			      ->andFilterWhere(['like', 'mainPart.part_name', $this->part_nm])
			      ->andFilterWhere(['like', 'mainPart.part_color', $this->part_color])
			      ->andFilterWhere(['like', 'part.part_name', $this->sub_part_nm]);
			if($mode == 'excel'){
				$query->orderBy([
					                'part_id' => SORT_ASC,
					                'sub_part_id' => SORT_ASC
				                ]);
				$file = Yii::createObject([
					                          'class' => 'codemix\excelexport\ExcelFile',
					                          'sheets' => [
						                          'BOM' => [
							                          'class' => 'codemix\excelexport\ActiveExcelSheet',
							                          'query' => $query,
							                          'attributes' => [
								                          'id',
								                          'part.part_no',
								                          'part.part_name',
								                          'part.part_color',
								                          'part.stateText',
								                          'part.statusText',
								                          'subPart.part_no',
								                          'subPart.part_name',
								                          'subPart.part_color',
								                          'usage_qty',
								                          'subPart.unit.unit_value',
								                          'warehouse.name',
								                          'subPart.stateText',
								                          'subPart.statusText',
								                          'createdBy.fullname',
								                          'createdAtFormatted',
								                          'updatedBy.fullname',
								                          'updatedAtFormatted',
								                          'remark',
								                          'statusText',
							                          ],
							                          'titles' => [
								                          1 => Yii::t('app', 'Product model'),
								                          2 => Yii::t('app', 'Part No'),
								                          3 => Yii::t('app', 'Part name'),
								                          4 => Yii::t('app', 'Part color'),
								                          5 => Yii::t('app', 'Part state'),
								                          6 => Yii::t('app', 'Part status'),
								                          7 => Yii::t('app', 'Sub part No'),
								                          8 => Yii::t('app', 'Sub part name'),
								                          9 => Yii::t('app', 'Sub part color'),
								                          11 => Yii::t('app', 'Unit'),
								                          12 => Yii::t('app', 'ULOC'),
								                          13 => Yii::t('app', 'Sub part state'),
								                          14 => Yii::t('app', 'Sub part status'),
								                          15 => Yii::t('app', 'Created by'),
								                          16 => Yii::t('app', 'Created at'),
								                          17 => Yii::t('app', 'Updated by'),
								                          18 => Yii::t('app', 'Updated at'),
								                          20 => Yii::t('app', 'Status'),
							                          ],
						                          ],
					                          ]
				                          ]);
				return $file;
			}else if($mode == 'part-raw-excel'){
				$query = "
					SELECT pt_part_no, pt_part_name, pt_part_color, pu_unit, spt_part_no, spt_part_name, spt_part_color, spu_unit, usage_qty, wh_name uloc, level, pt_remark remark FROM 
					(
						select * from 
						(SELECT * FROM part_part_wide where type = 'R') pp  
						LEFT JOIN 
						(SELECT id pt_id, part_no pt_part_no, part_name pt_part_name, part_color pt_part_color, unit_id pt_unit_id, remark pt_remark FROM part WHERE state=2) as pt
						ON pt.pt_id = pp.part_id
						LEFT JOIN 
						(SELECT id spt_id, part_no spt_part_no, part_name spt_part_name, part_color spt_part_color, unit_id spt_unit_id, remark spt_remark FROM part WHERE state=0) as spt
						ON spt.spt_id = pp.sub_part_id
						LEFT JOIN
						(SELECT id pu_id, unit_value pu_unit FROM unit) pu
						ON pt.pt_unit_id = pu.pu_id 
						LEFT JOIN
						(SELECT id spu_id, unit_value spu_unit FROM unit) spu
						ON spt.spt_unit_id = spu.spu_id
						LEFT JOIN
						(SELECT id wh_id, name wh_name FROM warehouse) wh
						ON pp.warehouse_id = wh.wh_id
					) bom
					WHERE pt_part_no IS NOT NULL
					ORDER BY pt_part_no, spt_part_no, uloc, usage_qty
				";

				$data = Yii::$app->db->createCommand($query,[':type' => PartPartWide::TYPE_RAW])->queryAll();
				$pt_part_no = null;
				$q=0;
				$excel_data=[];
				foreach($data as $index => $record){
					$temp_part_no = $record['pt_part_no'];
					if($pt_part_no == $temp_part_no){
						$excel_data[$q]['pt_part_no'] = '';
						$excel_data[$q]['pt_part_name'] = '';
						$excel_data[$q]['pt_part_color'] = '';
						$excel_data[$q]['pu_unit'] = '';
						$excel_data[$q]['spt_part_no'] = $record['spt_part_no'];
						$excel_data[$q]['spt_part_name'] = $record['spt_part_name'];
						$excel_data[$q]['spt_part_color'] = $record['spt_part_color'];
						$excel_data[$q]['spu_unit'] = $record['spu_unit'];
						$excel_data[$q]['usage_qty'] = $record['usage_qty'];
						$excel_data[$q]['uloc'] = $record['uloc'];
						$excel_data[$q]['level'] = $record['level'];
						$excel_data[$q]['remark'] = $record['remark'];
					}else{
						$excel_data[$q]['pt_part_no'] = $record['pt_part_no'];
		        $excel_data[$q]['pt_part_name'] = $record['pt_part_name'];
		        $excel_data[$q]['pt_part_color'] = $record['pt_part_color'];
		        $excel_data[$q]['pu_unit'] = $record['pu_unit'];
		        $excel_data[$q]['spt_part_no'] = $record['spt_part_no'];
		        $excel_data[$q]['spt_part_name'] = $record['spt_part_name'];
		        $excel_data[$q]['spt_part_color'] = $record['spt_part_color'];
		        $excel_data[$q]['spu_unit'] = $record['spu_unit'];
		        $excel_data[$q]['usage_qty'] = $record['usage_qty'];
		        $excel_data[$q]['uloc'] = $record['uloc'];
		        $excel_data[$q]['level'] = $record['level'];
		        $excel_data[$q]['remark'] = $record['remark'];
					}
					$q++;
					$pt_part_no = $temp_part_no;
				}

				$file = Yii::createObject(
					[
						'class' => 'codemix\excelexport\ExcelFile',
						'sheets' => [
							'Parts' => [
								'data' => $data,
								'titles' => [
									Yii::t('app', 'Part No'),
									Yii::t('app', 'Part name'),
									Yii::t('app', 'Part color'),
									Yii::t('app', 'Unit'),
									Yii::t('app', 'Sub part no'),
									Yii::t('app', 'Sub part name'),
									Yii::t('app', 'Sub part color'),
									Yii::t('app', 'Unit'),
									Yii::t('app', 'Qty'),
									Yii::t('app', 'Uloc'),
									Yii::t('app', 'Level'),
									Yii::t('app', 'Remark')
								],
								'styles' => [
									'A1:M1' => [
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
								'on afterRender' => function($event){
									$sheet = $event->sender->getSheet();
									$sheet->freezePane('B2');
								}
							],
							'Part group' => [
								'data' => $excel_data,
								'titles' => [
									Yii::t('app', 'Part No'),
									Yii::t('app', 'Part name'),
									Yii::t('app', 'Part color'),
									Yii::t('app', 'Unit'),
									Yii::t('app', 'Sub part no'),
									Yii::t('app', 'Sub part name'),
									Yii::t('app', 'Sub part color'),
									Yii::t('app', 'Unit'),
									Yii::t('app', 'Qty'),
									Yii::t('app', 'Uloc'),
									Yii::t('app', 'Level'),
									Yii::t('app', 'Remark')
								],
								'styles' => [
									'A1:M1' => [
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
								'on afterRender' => function($event){
									$sheet = $event->sender->getSheet();
									$sheet->freezePane('B2');
								}
							],
						]
					]);

				return $file;
			}else{
				return $dataProvider;
			}
		}
	}