<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * VisitorSearch represents the model behind the search form of `app\models\Visitor`.
 */
class VisitorSearch extends Visitor {

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['id'], 'integer'],
      [['page', 'filter_from', 'filter_to', 'controller', 'user_id', 'action', 'user_ip', 'visited_at'], 'safe'],
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
    $query = Visitor::find();
    $query->joinWith(['user']);
    //$query->andWhere(['<>','user_id',1]);
    //$query->andWhere(['<>','controller','index']);
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
    // grid filtering conditions
    $query->andFilterWhere([
      'visitor.id' => $this->id,
      'visitor.visited_at' => $this->visited_at,
    ]);
    $query->andFilterWhere(['like', 'controller', $this->controller])
          ->andFilterWhere(['like', 'action', $this->action])
          ->andFilterWhere(['like', 'user.fullname', $this->user_id])
          ->andFilterWhere(['like', 'user_ip', $this->user_ip])
          ->andFilterWhere(['like', "concat(controller,'/',action)", $this->page]);
    if (!empty($this->filter_from) and !empty($this->filter_to)) {
      $query->andFilterWhere(
        ['between', 'visited_at', $this->filter_from, $this->filter_to]);
    }
    $dataProvider->sort->attributes['user_id'] = [
      'asc' => ['user.fullname' => SORT_ASC],
      'desc' => ['user.fullname' => SORT_DESC],
    ];
    $dataProvider->sort->attributes['page'] = [
      'asc' => ["concat(controller,'/',action)" => SORT_ASC],
      'desc' => ["concat(controller,'/',action)" => SORT_DESC],
    ];
    if ($mode == 'excel') {
      $query->orderBy(['id' => SORT_DESC]);
      $file = Yii::createObject([
        'class' => 'codemix\excelexport\ExcelFile',
        'sheets' => [
          'Downtime' => [
            'class' => 'codemix\excelexport\ActiveExcelSheet',
            'query' => $query,
            'attributes' => [
              'id',
              'user.fullname',
              'pageroute',
              'user_ip',
              'visited_at'
            ],
            'titles' => [
              1 => Yii::t('app', 'User'),
              2 => Yii::t('app', 'Page')
            ],
          ]
        ]
      ]);

      return $file;
    } else {
      return $dataProvider;
    }
  }

}
