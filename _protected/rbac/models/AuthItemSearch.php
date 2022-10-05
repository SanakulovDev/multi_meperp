<?php

namespace app\rbac\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AuthItemSearch represents the model behind the search form of `app\rbac\models\AuthItem`.
 */
class AuthItemSearch extends AuthItem
{
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['name', 'description', 'rule_name', 'data'], 'safe'],
      [['type', 'created_at', 'updated_at'], 'integer'],
    ];
  }

  /**
   * {@inheritdoc}
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
    $query = AuthItem::find();

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
    ]);

    $this->load($params);
    $query->where(['type' => AuthItem::TYPE_ROLE])->andWhere(['!=','name','superadmin']);
    if (!$this->validate()) {
      return $dataProvider;
    }

    // grid filtering conditions
    $query->andFilterWhere([
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ]);

    $query->andFilterWhere(['like', 'name', $this->name])
      ->andFilterWhere(['like', 'description', $this->description])
      ->andFilterWhere(['like', 'rule_name', $this->rule_name])
      ->andFilterWhere(['like', 'data', $this->data]);

    return $dataProvider;
  }
}
