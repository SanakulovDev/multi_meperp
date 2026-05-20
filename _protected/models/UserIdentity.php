<?php
	namespace app\models;

	use Yii;
	use yii\base\Exception;
	use yii\base\InvalidConfigException;
	use yii\base\NotSupportedException;
	use yii\db\ActiveRecord;
	use yii\helpers\ArrayHelper;
	use yii\web\IdentityInterface;

	/**
	 * UserIdentity class for "user" table.
	 * This is a base user class that is implementing IdentityInterface.
	 * User model should extend from this model, and other user related models should
	 * extend from User model.
	 * @property integer $id
	 * @property string  $username
	 * @property string  $password_hash
	 * @property string  $password_plain
	 * @property string  $password_reset_token
	 * @property string  $email
	 * @property string  $account_activation_token
	 * @property string  $auth_key
	 * @property integer $status
	 * @property integer $created_at
	 * @property integer $updated_at
	 */
	class UserIdentity extends ActiveRecord implements IdentityInterface{
		/**
		 * Declares the name of the database table associated with this AR class.
		 * @return string
		 */
		public static function tableName(){
			return '{{%user}}';
		}

//------------------------------------------------------------------------------------------------//
// IDENTITY INTERFACE IMPLEMENTATION
//------------------------------------------------------------------------------------------------//
		/**
		 * Finds an identity by the given ID.
		 * @param int|string $id The user id.
		 * @return IdentityInterface|static
		 */
		public static function findIdentity($id){
			return static::findOne(['id' => $id, 'status' => User::STATUS_ACTIVE]);
		}

		/**
		 * Finds an identity by the given access token.
		 * @param mixed $token
		 * @param null  $type
		 * @return void|IdentityInterface
		 * @throws NotSupportedException
		 */
		public static function findIdentityByAccessToken($token, $type = null){
			return static::findOne(['access_token' => $token]);
		}

		/**
		 * Returns an ID that can uniquely identify a user identity.
		 * @return int|mixed|string
		 */
		public function getId(){
			return $this->getPrimaryKey();
		}

		/**
		 * Returns a key that can be used to check the validity of a given
		 * identity ID. The key should be unique for each individual user, and
		 * should be persistent so that it can be used to check the validity of
		 * the user identity. The space of such keys should be big enough to defeat
		 * potential identity attacks.
		 * @return string
		 */
		public function getAuthKey(){
			return $this->auth_key;
		}

		/**
		 * Validates the given auth key.
		 * @param string $authKey The given auth key.
		 * @return boolean          Whether the given auth key is valid.
		 */
		public function validateAuthKey($authKey){
			return $this->getAuthKey() === $authKey;
		}

//------------------------------------------------------------------------------------------------//
// IMPORTANT IDENTITY HELPERS
//------------------------------------------------------------------------------------------------//
		/**
		 * Generates "remember me" authentication key.
		 */
		public function generateAuthKey(){
			$this->auth_key = Yii::$app->security->generateRandomString();
		}

		/**
		 * Validates password.
		 * @param string $password
		 * @return bool
		 * @throws InvalidConfigException
		 */
		public function validatePassword($password){
			return Yii::$app->security->validatePassword($password, $this->password_hash);
		}

		/**
		 * Generates password hash from password and sets it to the model.
		 * @param string $password
		 * @throws Exception
		 * @throws InvalidConfigException
		 */
		public function setPassword($password){
			if($this->hasAttribute('password_plain')){
				$this->password_plain = $password;
			}
			$this->password_hash = Yii::$app->security->generatePasswordHash($password);
		}

		public function getDisplayPassword(){
			if(!empty($this->password_plain)){
				return $this->password_plain;
			}

			$possiblePasswords = [
				$this->username,
				'admin1234',
				'shipper1234',
				'qlikPassword2020',
			];

			foreach(array_unique(array_filter($possiblePasswords)) as $password){
				try{
					if($this->validatePassword($password)){
						return $password;
					}
				}catch(InvalidConfigException $exception){
					return '******';
				}
			}

			return '******';
		}

		public function getRole(){
			// User has_one Role via Role.user_id -> id
			return $this->hasOne(Role::className(), ['user_id' => 'id']);
		}

		public function getRoleName(){
			// if user has some role assigned, return it's name
			if($this->role){
				return $this->role->item_name;
			}
			// user does not have role assigned, but if he is authenticated '@'
			return '@uthenticated';
		}

		public function getUserWarehouses(){
			return $this->hasMany(UserWarehouse::className(), ['user_id' => 'id']);
		}

		public function getWarehouses(){
			return $this->hasMany(Warehouse::className(), ['id' => 'warehouse_id'])->viaTable('user_warehouse', ['user_id' => 'id']);
		}

		public function getReports(){
			return $this->hasMany(Report::className(), ['id' => 'report_id'])->viaTable('user_report', ['user_id' => 'id']);
		}

		public function getWarehouseIds(){
			return ArrayHelper::map($this->warehouses, 'id', 'id');
		}

		public function getReportIds(){
			return ArrayHelper::map($this->reports, 'id', 'id');
		}

		public function getWarehouseNames(){
			return ArrayHelper::map(Warehouse::find()->all(), 'id', 'name');
		}

		public function getWarehouseTypes(){
			return ArrayHelper::map($this->warehouses, 'id', 'warehouse_type');
		}

		public function getReportDescriptions(){
			return ArrayHelper::map($this->reports, 'id', 'description');
		}

	}
