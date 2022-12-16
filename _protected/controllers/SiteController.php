<?php

namespace app\controllers;

use app\components\Helpers;
use app\models\AccountActivation;
use app\models\ContactForm;
use app\models\LoginForm;
use app\models\PasswordResetRequestForm;
use app\models\ProductionMonitor;
use app\models\ResetPasswordForm;
use app\models\SignupForm;
use app\models\User;
use app\models\ProductionPlan;
use app\models\ProductionOrder;
use app\models\SalesPlan;
use app\rbac\models\Role;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Site controller.
 * It is responsible for displaying static pages, logging users in and out,
 * sign up and account activation, and password reset.
 */
class SiteController extends Controller
{
	/**
	 * Returns a list of behaviors that this component should behave as.
	 * @return array
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['logout', 'index'],
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}

	/**
	 * Declares external actions for the controller.
	 * @return array
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
		];
	}

	//------------------------------------------------------------------------------------------------//
	// STATIC PAGES
	//------------------------------------------------------------------------------------------------//
	/**
	 * Displays the index (home) page.
	 * Use it in case your home page contains static content.
	 * @return string
	 */
	public function actionIndex()
	{
		switch (Yii::$app->user->identity->roleName) {
			case 'superadmin':
			case 'admin':
				$route = 'site/admin-dashboard';
				break;
			case 'report':
				$route = 'report/index';
				break;
			case 'logistics':
				$route = 'container-invoice/index';
				break;
			case 'buyer':
				$route = 'contract/index';
				break;
			case 'plan':
				$route = 'production-plan/index';
				break;
			case 'pe':
				$route = 'part-part/index';
				break;
			case 'counter':
				$route = 'production-order/create';
				break;
			case 'crusher':
				$route = 'crushing/index';
				break;
			case 'sales':
			case 'shipper':
				$route = 'fg-invoice/index';
				break;
			case 'declarant':
				$route = 'gtd/index';
				break;
			default:
				$route = 'document/index';
				break;
		}
		return $this->redirect($route);
	}

	public function actionAdminDashboard()
	{
		if (!Yii::$app->user->can('site-admin-dashboard')) {
			throw new HttpException(403, Yii::t('app', 'You are not allowed to do this action.'));
		}
		$data = [];
		// users
		$query = "
        select au.item_name role, count(*) cnt from 
          user u, auth_assignment au 
        where 
          u.id = au.user_id and
          u.status = 10
        group by au.item_name
        order by cnt desc
      ";
		$result = Yii::$app->db->createCommand($query)->queryAll();
		$data['roles'] = $result;
		$total = 0;
		$chart_data = [];
		foreach ($data['roles'] as $key => $row) {
			$total += $row['cnt'];
			$random_color = Helpers::random_color();
			$chart_data[] = [
				'value' => $row['cnt'],
				'color' => $random_color,
				'highlight' => $random_color,
				'label' => $row['role']
			];
			$data['roles'][$key]['color'] = $random_color;
		}
		$data['users_data'] = json_encode($chart_data);
		$data['users_cnt'] = $total;
		// *****
		// docs
		$query = "
        select dt.code, count(*) cnt from 
          document d left join document_type dt on d.document_type_id = dt.id
        group by dt.code
        order by cnt desc
      ";
		$result = Yii::$app->db->createCommand($query)->queryAll();
		$data['docs'] = $result;
		$total = 0;
		$chart_data = [];
		foreach ($data['docs'] as $key => $row) {
			$total += $row['cnt'];
			$random_color = Helpers::random_color();
			$chart_data[] = [
				'value' => $row['cnt'],
				'color' => $random_color,
				'highlight' => $random_color,
				'label' => $row['code']
			];
			$data['docs'][$key]['color'] = $random_color;
		}
		$data['docs_data'] = json_encode($chart_data);
		$data['docs_cnt'] = $total;
		// *****
		// whs
		$query = "
       select 
        CASE
            WHEN warehouse_type = 0 THEN 'Физ.'
            WHEN warehouse_type = 1 THEN 'Произ.'
            WHEN warehouse_type = 2 THEN 'Вирт.'
            WHEN warehouse_type = 3 THEN 'Аутс.'
          end as 'type_name',
        warehouse_type, 
        count(*) cnt from 
        warehouse
      group by warehouse_type
      order by cnt desc
      ";
		$result = Yii::$app->db->createCommand($query)->queryAll();
		$data['whs'] = $result;
		$total = 0;
		$chart_data = [];
		foreach ($data['whs'] as $key => $row) {
			$total += $row['cnt'];
			$random_color = Helpers::random_color();
			$chart_data[] = [
				'value' => $row['cnt'],
				'color' => $random_color,
				'highlight' => $random_color,
				'label' => $row['type_name']
			];
			$data['whs'][$key]['color'] = $random_color;
		}
		$data['whs_data'] = json_encode($chart_data);
		$data['whs_cnt'] = $total;
		// *****
		// parts
		$query = "
       select 
        CASE
            WHEN state = 0 THEN 'RAW'
            WHEN state = 1 THEN 'SEMI'
            WHEN state = 2 THEN 'FINISH'
          end as 'state_name',
        state, 
        count(*) cnt from 
        part
      where status = 1
      group by state
      order by cnt desc
      ";
		$result = Yii::$app->db->createCommand($query)->queryAll();
		$data['parts'] = $result;
		$total = 0;
		$chart_data = [];
		foreach ($data['parts'] as $key => $row) {
			$total += $row['cnt'];
			$random_color = Helpers::random_color();
			$chart_data[] = [
				'value' => $row['cnt'],
				'color' => $random_color,
				'highlight' => $random_color,
				'label' => $row['state_name']
			];
			$data['parts'][$key]['color'] = $random_color;
		}
		$data['parts_data'] = json_encode($chart_data);
		$data['parts_cnt'] = $total;
		// *****
		// docs line
		$from = date("Y-m-d", strtotime('-30 days'));
		$to = date("Y-m-d");
		$query = "
            select docdate, count(*) count from document 
            where docdate between '" . $from . "' and '" . $to . "'  
            group by docdate 
            order by docdate     
      ";
		$result = Yii::$app->db->createCommand($query)->queryAll();
		$chart_data = [];
		foreach ($result as $key => $row) {
			$chart_data['titles'][] = $row['docdate'];
			$chart_data['values'][] = $row['count'];
		}
		$data['docs_line_data'] = json_encode($chart_data);
		// *****
		// visitors line
		$from = date("Y-m-d", strtotime('-30 days'));
		$to = date("Y-m-d");
		$filterAdminIds = count($this->adminids)>0 ?  " and user_id not in (" . implode(',', $this->adminids) . ")" : "";
		$query = "
       select date(visited_at) visited_at, count(*) count from visitor 
            where visited_at between '$from' and '$to' $filterAdminIds
            and concat(`controller`,'/',`action`) not in ('site/index','site/login','site/logout','part/get-partname','part/get-parts-by-floc')
            group by date(visited_at) 
            order by date(visited_at)     
      ";
		$result = Yii::$app->db->createCommand($query)->queryAll();
		$chart_data = [];
		foreach ($result as $key => $row) {
			$chart_data['titles'][] = $row['visited_at'];
			$chart_data['values'][] = $row['count'];
		}
		$data['visitors_data'] = json_encode($chart_data);
		// *****

    $data['productionMonitor'] = self::getProductionMonitorForRecentWeek();


// table 1 coverage
	$controller = new \app\controllers\ReportController('site', $this->module);
	$localCoverageToday = $controller->localCoverage;

// production plan
	$productionPlanToday = ProductionPlan::find()
		->where(['DATE_FORMAT(production_date, "%Y-%m-%d" )' =>  date("Y-m-d", time())])
		->all();
	$productionPlanTomorrow = ProductionPlan::find()
		->where(['DATE_FORMAT(production_date, "%Y-%m-%d" )' =>  date("Y-m-d", strtotime("+1 day"))])
		->all();
// sales plan
	$salesPlanMonth = SalesPlan::find()
		->where(['DATE_FORMAT(target_date, "%Y-%m" )' =>  date("Y-m")])
		->all();

	// fact todays
	$begin_date = date("Y-m-d")." 00:00:00";
	$end_date = date("Y-m-d H:i:s");
	$factTodays	= ProductionOrder::find()
		->where(['between','FROM_UNIXTIME(created_at, "%Y-%m-%d %H:%i:%s")', $begin_date, $end_date])
		->andWhere(['is_label' => 2])
		->all();
	// fact tomorrow
	// $begin_date =
	$factTomorrow = ProductionOrder::find()
		->where(['>=','FROM_UNIXTIME(created_at, "%Y-%m-%d %h" )',  date("Y-m-d h", strtotime("+1 day"))])
		->andWhere(['is_label' => 2])
		->all();
	// vd($localCoverageToday[0]);
		return $this->render('admin-dashboard', [
			'data' 						=> $data,
			'productionPlanToday' 		=> $productionPlanToday,
			'productionPlanTomorrow' 	=> $productionPlanTomorrow,
			'factTodays' 				=> $factTodays,
			'factTomorrow' 				=> $factTomorrow,
			'salesPlanMonth' 			=> $salesPlanMonth,
			'localCoverageToday' 		=> $localCoverageToday,
		]);
	}

  private function getProductionMonitorForRecentWeek() {
    $to = date('Y-m-d');
    $from = date('Y-m-d', strtotime('-7 days'));

    $query = "select w.name, pm.shift, pm.production_date, pm.status 
      from production_monitor pm 
      inner join warehouse w on pm.warehouse_id = w.id
      where pm.production_date>=:from and pm.production_date<=:to 
      ORDER by pm.production_date, pm.shift";
    $result = Yii::$app->db->createCommand($query, ['from'=>$from, 'to'=>$to])->queryAll();
    $dates = [];
    $warehouses = [];
    $list = [];

    foreach ($result as $row) {
      $dates[] = $row['production_date'];
      $warehouses[] = $row['name'];
      $k = implode(';', [$row['production_date'], $row['shift'], $row['name']]);
      $list[$k] = $row['status'];
    }

    $warehouses = array_unique($warehouses);
    $dates = array_unique($dates);

    return compact('dates','warehouses', 'list');
	}

	protected function getAdminids()
	{
		return ArrayHelper::map(Role::find()->where(['item_name' => 'admin'])->all(), 'user_id', 'user_id');
	}





	//------------------------------------------------------------------------------------------------//
	// LOG IN / LOG OUT / PASSWORD RESET
	//------------------------------------------------------------------------------------------------//
	/**
	 * Logs in the user if his account is activated,
	 * if not, displays appropriate message.
	 * @return string|Response
	 */
	public function actionLogin()
	{
		$this->layout = 'adminltelogin';
		// user is logged in, he doesn't need to login
		if (!Yii::$app->user->isGuest) {
			return $this->goHome();
		}
		//$lwe = Yii::$app->params['lwe'];
		$lwe = false;
		// if 'lwe' value is 'true' we instantiate LoginForm in 'lwe' scenario
		$model = $lwe ? new LoginForm(['scenario' => 'lwe']) : new LoginForm();
		// monitor login status
		$successfulLogin = true;
		// posting data or login has failed
		if (!$model->load(Yii::$app->request->post()) || !$model->login()) {
			$successfulLogin = false;
		}
		// if user's account is not activated, he will have to activate it first
		if ($model->status === User::STATUS_INACTIVE && $successfulLogin === false) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'Your account is blocked.'));
			return $this->refresh();
		}
		// if user is not denied because he is not active, then his credentials are not good
		if ($successfulLogin === false) {
			return $this->render('login', ['model' => $model]);
		}
		// login was successful, let user go wherever he previously wanted
		return $this->goBack();
	}

	/**
	 * Logs out the user.
	 * @return Response
	 */
	public function actionLogout()
	{
		Yii::$app->user->logout();
		return $this->goHome();
	}

	public function actionDebug(){
		
		// \app\models\ProductionMonitor::write(33,'2021-03-03',1);
		// \app\models\ProductionMonitor::confirm(1,1);
		// \app\models\ProductionMonitor::complete(1,1);
		// print_r(\app\components\Helpers::getShift('2020-05-02 22:40'));
		\app\models\PartProductionMonitor::setProduced(1, 1, 100, 1);
		

	}
}
