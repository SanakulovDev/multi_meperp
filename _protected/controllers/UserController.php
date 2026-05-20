<?php
	namespace app\controllers;

	use app\models\User;
	use app\models\UserAccess;
	use app\models\UserReport;
	use app\models\UserSearch;
	use app\models\UserWarehouse;
	use Exception;
	use Yii;
	use yii\web\ForbiddenHttpException;
	use yii\web\NotFoundHttpException;
	use yii\web\Response;
	use yii\web\ServerErrorHttpException;

	/**
		* UserController implements the CRUD actions for User model.
		*/
	class UserController extends AppController{

		protected $_pageSize = 11;

		/**
			* Lists all User models.
			* @return string
			*/
		public function actionIndex(){
			$searchModel = new UserSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->_pageSize);
      $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		/**
			* Displays a single User model.
			* @param integer $id The user id.
			* @return string
			* @throws NotFoundHttpException
			*/
		public function actionView($id){
			return $this->render('view', ['model' => $this->findModel($id)]);
		}

		/**
			* Creates a new User model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return string|Response
			*/
		public function actionCreate(){
			$user = new User();
      $user->account_suffix = Yii::$app->params['account_suffix'];
			if(!$user->load(Yii::$app->request->post())){
				return $this->render('create', ['user' => $user]);
			}
			if(!empty($user->password)){
				$user->setPassword($user->password);
				$user->password_plain = $user->password;
			}else{
				$user->setPassword($user->username);
				$user->password_plain = $user->username;
			}
			$user->generateAuthKey();
			if(!$user->save()){
				return $this->render('create', ['user' => $user]);
			}
			if(!empty($_POST['User']['warehouse_ids'])){
				foreach($_POST['User']['warehouse_ids'] as $warehouse_id){
					if(!empty($warehouse_id)){
						$userWarehouse = new UserWarehouse();
						$userWarehouse->user_id = $user->id;
						$userWarehouse->warehouse_id = $warehouse_id;
						if(!$userWarehouse->save()){
							echo "<pre>";
							print_r($userWarehouse->errors);
							echo "</pre>";
							die;
						}
					}
				}
			}
			if(!empty($_POST['User']['report_ids'])){
				foreach($_POST['User']['report_ids'] as $report_id){
					if(!empty($report_id)){
						$userReport = new UserReport();
						$userReport->user_id = $user->id;
						$userReport->report_id = $report_id;
						if(!$userReport->save()){
							Yii::$app->session->setFlash('error', $userReport->errors);
						}
					}
				}
			}
			$auth = Yii::$app->authManager;
			$role = $auth->getRole($user->item_name);
			$info = $auth->assign($role, $user->getId());
			if(!$info){
				Yii::$app->session->setFlash('error', Yii::t('app', 'There was some error while saving user role.'));
			}
			return $this->redirect('index');
		}

		/**
			* Updates an existing User and Role models.
			* If update is successful, the browser will be redirected to the 'view' page.
			* @param integer $id The user id.
			* @return string|Response
			* @throws NotFoundHttpException
			*/
		public function actionUpdate($id){
			// load user data
			$user = $this->findModel($id);
      
      
      if(Yii::$app->user->identity->rolename == 'admin'){
        if($user->role->item_name == 'superadmin'){
          Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
          return $this->redirect(['index']);
        }
			}
      
			$user->warehouse_ids = $user->warehouseIds;
			$user->report_ids = $user->reportIds;
			$auth = Yii::$app->authManager;
			// get user role if he has one
			if($roles = $auth->getRolesByUser($id)){
				// it's enough for us the get first assigned role name
				$role = array_keys($roles)[0];
			}
			// if user has role, set oldRole to that role name, else offer 'member' as sensitive default
			$oldRole = (isset($role)) ? $auth->getRole($role) : $auth->getRole('member');
			// set property item_name of User object to this role name, so we can use it in our form
			$user->item_name = $oldRole->name;
			if(!$user->load(Yii::$app->request->post())){
				return $this->render('update', ['user' => $user, 'role' => $user->item_name]);
			}
			// only if user entered new password we want to hash and save it
			if($user->password){
				$user->setPassword($user->password);
				$user->password_plain = $user->password;
			}
			// if admin is activating user manually we want to remove account activation token
			if($user->status == User::STATUS_ACTIVE && $user->account_activation_token != null){
				$user->removeAccountActivationToken();
			}
			if(!$user->save()){
				return $this->render('update', ['user' => $user, 'role' => $user->item_name]);
			}
			UserWarehouse::deleteAll(['user_id' => $user->id]);
			if(!empty($_POST['User']['warehouse_ids'])){
				foreach($_POST['User']['warehouse_ids'] as $warehouse_id){
					if(!empty($warehouse_id)){
						$userWarehouse = new UserWarehouse();
						$userWarehouse->user_id = $user->id;
						$userWarehouse->warehouse_id = $warehouse_id;
						if(!$userWarehouse->save()){
							echo "<pre>";
							print_r($userWarehouse->errors);
							echo "</pre>";
							die;
						}
					}
				}
			}
			UserReport::deleteAll(['user_id' => $user->id]);
			if(!empty($_POST['User']['report_ids'])){
				foreach($_POST['User']['report_ids'] as $report_id){
					if(!empty($report_id)){
						$userReport = new UserReport();
						$userReport->user_id = $user->id;
						$userReport->report_id = $report_id;
						if(!$userReport->save()){
							Yii::$app->session->setFlash('error', $userReport->errors);
						}
					}
				}
			}
			// take new role from the form
			$newRole = $auth->getRole($user->item_name);
			// get user id too
			$userId = $user->getId();
			// we have to revoke the old role first and then assign the new one
			// this will happen if user actually had something to revoke
			if($auth->revoke($oldRole, $userId)){
				$info = $auth->assign($newRole, $userId);
			}
			// in case user didn't have role assigned to him, then just assign new one
			if(!isset($role)){
				$info = $auth->assign($newRole, $userId);
			}
			if(!$info){
				Yii::$app->session->setFlash('error', Yii::t('app', 'There was some error while saving user role.'));
			}
			return $this->redirect(['index']);
		}

		public function actionGetInfo($q){
			try{
				//Yii::$app->response->format = Response::FORMAT_JSON;
				Yii::$app->response->format = Response::FORMAT_JSON;
				//$user = Yii::$app->ldap->find($q);
				$user = Yii::$app->ad->search()->findBy('sAMAccountname', $q);
				if($user){
					$res['username'] = $user->samaccountname[0];
					$res['fullname'] = $user->cn[0];
					$res['employer_id'] = $user->employeenumber[0];
					$res['mail'] = $user->mail[0];
					$res['foto'] = ($user->thumbnailphoto[0]) ? base64_encode($user->thumbnailphoto[0]) : "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";
					$distinguishedname = $user->distinguishedname[0];
					$distinguishedname = substr($distinguishedname, strpos($distinguishedname, 'DC'), 1000);
					$res['account_suffix'] = str_replace(',', '.', str_ireplace('dc=', '', $distinguishedname));
				}else{
					return false;
				}
				return $res;
			}catch(Exception $ex){
				return $ex->getMessage();
			}
		}

		/**
			* Deletes an existing User model.
			* If deletion is successful, the browser will be redirected to the 'index' page.
			* @param integer $id The user id.
			* @return Response
			* @throws NotFoundHttpException
			*/
		public function actionDelete($id){
      
      $user = $this->findModel($id);
      
      if(Yii::$app->user->identity->rolename == 'admin'){
        if($user->role->item_name == 'superadmin'){
          Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
          return $this->redirect(['index']);
        }
			}
      
			// delete user or throw exception if could not
			if(!$user->delete()){
				throw new ServerErrorHttpException(Yii::t('app', 'We could not delete this user.'));
			}
			$auth = Yii::$app->authManager;
			$info = true; // monitor info status
			// get user role if he has one
			if($roles = $auth->getRolesByUser($id)){
				// it's enough for us the get first assigned role name
				$role = array_keys($roles)[0];
			}
			// remove role if user had it
			if(isset($role)){
				$info = $auth->revoke($auth->getRole($role), $id);
			}
			if(!$info){
				Yii::$app->session->setFlash('error', Yii::t('app', 'There was some error while deleting user role.'));
				return $this->redirect(['index']);
			}
			Yii::$app->session->setFlash('success', Yii::t('app', 'You have successfuly deleted user and his role.'));
			return $this->redirect(['index']);
		}

		/**
			* Finds the User model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id The user id.
			* @return User The loaded model.
			* @throws NotFoundHttpException if the model cannot be found.
			*/
		protected function findModel($id){
			$model = User::findOne($id);
			if(is_null($model)){
				throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
			}
			return $model;
		}

		public function actionXls(){
			ini_set('memory_limit', '-1');
			$searchModel = new UserSearch();
			$xls_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
			$xls_file->send('users_'.date("Ymd(His)").'.xlsx');
      die;
		}

//		public function beforeAction($action){
//			if(parent::beforeAction($action)){
//				if(!Yii::$app->user->can('user-index')){
//					throw new ForbiddenHttpException('Доступ ограничен !!!');
//				}
//				return true;
//			}else{
//				return false;
//			}
//		}
	}
