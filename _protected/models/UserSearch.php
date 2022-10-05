<?php

namespace app\models;

use codemix\excelexport\ActiveExcelSheet;
use codemix\excelexport\ExcelFile;
use Yii;
use app\rbac\models\AuthItem;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form for app\models\User.
 */
class UserSearch extends User
{
    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['username', 'fullname', 'email', 'status', 'item_name', 'tabno', 'account_suffix'], 'safe'],
        ];
    }

    /**
     * Returns a list of scenarios and the corresponding active attributes.
     *
     * @return array
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     * @param integer $pageSize How many users to display per page.
     * @return ActiveDataProvider
     */
    public function search($params, $mode = '')
    {
        $query = User::find()->joinWith('role');
        if (!Yii::$app->user->can('theCreator')) {
            $query->where(['!=', 'item_name', 'theCreator']);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_ASC]],
            'pagination' => ['pageSize' => 20]
        ]);
        // make item_name (Role) sortable
        $dataProvider->sort->attributes['item_name'] = [
            'asc' => ['item_name' => SORT_ASC],
            'desc' => ['item_name' => SORT_DESC],
        ];
        if ($mode != 'excel') {
          if (!($this->load($params) && $this->validate())) {
              return $dataProvider;
          }
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'account_suffix' => $this->account_suffix,
            'item_name' => $this->item_name
        ]);
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'tabno', $this->tabno]);

        if ($mode == 'excel') {
            $file = Yii::createObject([
                'class' => ExcelFile::className(),
                'sheets' => [
                    'Users' => [
                        'class' => ActiveExcelSheet::className(),
                        'query' => $query,
                        'attributes' => [
                            'id',
                            'tabno',
                            'username',
                            'fullname',
                            'email',
                            'roleName',
                            'statusText',
                            'createdAtFormatted',
                            'updatedAtFormatted',

                        ],
                        'titles' => [
                            6 => Yii::t('app', 'Status'),
                            7 => Yii::t('app', 'Created at'),
                            8 => Yii::t('app', 'Updated at'),
                        ],
                    ],
                ]
            ]);
            return $file;
        } else {
            return $dataProvider;
        }
    }

    /**
     * Returns the array of possible user roles.
     * NOTE: used in user/index view.
     *
     * @return mixed
     */
    public static function getRolesList()
    {
        $roles = [];
        foreach (AuthItem::getRoles() as $item_name) {
            $roles[$item_name->name] = $item_name->name;
        }

        return $roles;
    }
}
