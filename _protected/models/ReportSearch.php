<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ReportSearch represents the model behind the search form of `app\models\Report`.
 */
class ReportSearch extends Report{
	/**
	 * {@inheritdoc}
	 */
	public function rules(){
		return [
			[['id'], 'integer'],
			[['action', 'title', 'description'], 'safe'],
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
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params){
		
                $query = Report::find()->with(['userReports' => function($q){
			$q->andWhere(['user_id' => Yii::$app->user->identity->id]);
		}]);
                
		$dataProvider = new ActiveDataProvider(['query' => $query]);
		$this->load($params);
		if(!$this->validate()){
			return $dataProvider;
		}
		// grid filtering conditions
		$query->andFilterWhere(['id' => $this->id]);
		$query->andFilterWhere(['like', 'action', $this->action])
		      ->andFilterWhere(['like', 'title', $this->title])
		      ->andFilterWhere(['like', 'description', $this->description]);

		return $dataProvider;
	}
}
