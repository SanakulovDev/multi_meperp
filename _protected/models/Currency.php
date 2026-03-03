<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;
	use yii\helpers\ArrayHelper;

	/**
	 * This is the model class for table "currency".
	 * @property int             $id
	 * @property string          $code
	 * @property string          $name
	 * @property InvoiceDetail[] $invoiceDetails
   * @property FreightInvoice[] $freightInvoices
	 */
	class Currency extends ActiveRecord{
		/**
		 * @inheritdoc
		 */
		public static function tableName(){
			return 'currency';
		}

		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['code'], 'required'],
				[['code'], 'string', 'max' => 10],
				[['name'], 'string', 'max' => 50],
				[['code'], 'unique'],
			];
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels(){
			return [
				'id' => Yii::t('app', 'ID'),
				'code' => Yii::t('app', 'Code'),
				'name' => Yii::t('app', 'Name'),
			];
		}

		/**
		 * @return ActiveQuery
		 */
		public function getInvoiceDetails(){
			return $this->hasMany(InvoiceDetail::className(), ['currency_id' => 'id']);
		}

		public static function getCurrencyCodes(){
			return ArrayHelper::map(self::find()->all(), 'id', 'trimcode');
		}

		public function getTrimCode(){
			return trim($this->code);
		}

		public static function findOneCurrencyCode($code){
			return self::find()->where(['trim(code)' => trim($code)])->one();
		}

		public function getInvoices(){
			return $this->hasMany(Invoice::className(), ['currency_id' => 'id']);
		}

    public function getFreightInvoices() {
      return $this->hasMany(FreightInvoice::className(), ['currency_id' => 'id']);
    }

	}
