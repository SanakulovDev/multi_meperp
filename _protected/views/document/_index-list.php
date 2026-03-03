<?
  use app\models\DocumentType;
	use app\models\Warehouse;
	use yii\grid\GridView;
	use yii\helpers\Html;
	use yii\helpers\Url;
?>

<?=
	GridView::widget(
		[
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
			'options' => ['style' => 'overflow:auto;clear:both'],
			'emptyText' => Yii::t('app', 'No results found.'),
			'tableOptions' => [
				'class' => 'sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0',
				'data-step' => 4,
				'data-intro' => Yii::t('intro', 'data-table')
			],
			'filterRowOptions' => ['data-step' => 5, 'data-intro' => Yii::t('intro', 'filter')],
			'pager' => [
				'class' => '\yii\widgets\LinkPager',
				'options' => [
					'class' => 'pagination',
					'data-step' => 6,
					'data-intro' => Yii::t('intro', 'pagination')
				],
			],
			'columns' => [
				[
					'class' => 'yii\grid\SerialColumn',
					'header' => '№',
					'headerOptions' => ['style' => 'width: 40px;text-align: center;color: #3c8dbc;'],
					'contentOptions' => ['style' => 'width: 40px;text-align: center;']
				],
				[
					'class' => 'yii\grid\ActionColumn',
					'header' => '<i class="fa fa-fw fa-gears"></i>',
					'headerOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;color: #3c8dbc;'],
					'contentOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
					'template' => '{update}{delete}{confirm}{print}{history}{update-act}{delete-act}{update-local}{delete-local}{update-local-issue}{delete-local-issue}{update-local-kd}{delete-local-kd}{shop-disconfirm}',
					'buttons' => [
						'update' => function($url, $model){
							if(!empty($model->serial_number))
								return false;
							if($model->document_type_id != 2 or $model->isLocal or $model->isLocalIssue)
								return false;
							if($model->status == 1)
								return false;
							if(!Yii::$app->user->can('admin')){
								if(!Yii::$app->user->can('mrp')){
									return false;
								}else{
									if(!in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds))
										return false;
								}
							}
							$url = Url::toRoute(['document/update', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Edit')
								]).'&nbsp;';
						},
						'delete' => function($url, $model){
							if(!empty($model->serial_number) and !Yii::$app->user->can('counter'))
								return false;
							if($model->document_type_id != 2 or $model->isLocal or $model->isLocalIssue)
								return false;
							if($model->status == 1)
								return false;
							if(!Yii::$app->user->can('admin')){
								if(!(Yii::$app->user->can('mrp') or Yii::$app->user->can('counter'))){
									return false;
								}else{
									if(!in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds))
										return false;
								}
							}
							if(empty($model->serial_number)){
								$route = ['document/delete', 'id' => $model->id];
							}else{
								$route = ['document/delete-shop-consumption', 'id' => $model->id, 'view' => 'index'];
							};
							$url = Url::toRoute($route);
							return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Delete'),
									'data' => [
										'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
										'method' => 'post',
									],
								]).'&nbsp;';
						},
              
						'confirm' => function($url, $model){
							if(!empty($model->serial_number)) return false;
              
							if(
                !in_array($model->document_type_id,[1,2]) or 
                $model->isLocalkd or 
                $model->isLocal or 
                $model->isLocalIssue or 
                $model->isProdIssue)
								return false;
              
							if(!Yii::$app->user->can('admin')){
								if(!Yii::$app->user->can('mrp')){
									return false;
								}else{
									if(!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))
										return false;
								}
							}
              
              if($model->to_warehouse_id == Yii::$app->params['logxWhId'] and Yii::$app->params['logxWhId'] != Yii::$app->params['kdWhId']) 
                return false;
              
							$url = Url::toRoute(['document/confirm', 'id' => $model->id]);
              
							if($model->status == 1){
								$icon = 'remove';
								$title = Yii::t('app', 'Cancel');
							}else{
								$icon = 'ok';
								$title = Yii::t('app', 'Confirm');
							}
							return Html::a('<span class="glyphicon glyphicon-'.$icon.'" aria-hidden="true"></span>', $url, [
									'title' => $title,
									'data' => [
										'confirm' => Yii::t('app', 'Are you sure?'),
										'method' => 'post',
									],
								]).'&nbsp;';
						},
              
						'shop-disconfirm' => function($url, $model){
							if(empty($model->serial_number))
								return false;
							if($model->status != 1)
								return false;
							if($model->document_type_id != 2 or $model->isLocalkd or $model->isLocal or $model->isLocalIssue or $model->isProdIssue)
								return false;
							if(!Yii::$app->user->can('admin')){
								if(!Yii::$app->user->can('counter')){
									return false;
								}else{
									if(!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))
										return false;
								}
							}
							$url = Url::toRoute(['document/shop-disconfirm', 'id' => $model->id, 'view' => 'index']);
							$icon = 'remove';
							$title = Yii::t('app', 'Cancel');
							return Html::a('<span class="glyphicon glyphicon-'.$icon.'" aria-hidden="true"></span>', $url, [
									'title' => $title,
									'data' => [
										'confirm' => Yii::t('app', 'Are you sure?'),
										'method' => 'post',
									],
								]).'&nbsp;';
						},
						'print' => function($url, $model){
							if(!Yii::$app->user->can('admin')){
								if(!Yii::$app->user->can('mrp')){
									return false;
								}else{
									if(!in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds))
										return false;
								}
							}
							$url = Url::toRoute(['document/print', 'id' => $model->id]);
							return Html::a('<span class="fa fa-print" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Print')
								]).'&nbsp;';
						},
						'history' => function($url, $model){
							if(!Yii::$app->user->can('admin'))
								return false;
							$url = Url::toRoute(['history-document/index', 'HistoryDocumentSearch[document_id]' => $model->id]);
							return Html::a('<span class="fa fa-clock-o" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'History')
								]).'&nbsp;';
						},
						// mfu buttons
						'update-act' => function($url, $model){
							if($model->document_type_id != 3)
								return false;
							if(!(Yii::$app->user->can('mfu') or (Yii::$app->user->can('mrp') and Yii::$app->user->identity->act_access == 1)))
								return false;
							$url = Url::toRoute(['document/update-act', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-pencil text-danger" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Edit')
								]).'&nbsp;';
						},
						'delete-act' => function($url, $model){
							if($model->document_type_id != 3)
								return false;
							if(!(Yii::$app->user->can('mfu') or (Yii::$app->user->can('mrp') and Yii::$app->user->identity->act_access == 1)))
								return false;
							$url = Url::toRoute(['document/delete-act', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-trash text-danger" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Delete'),
									'data' => [
										'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
										'method' => 'post',
									],
								]).'&nbsp;';
						},
						// local receipt
						'update-local' => function($url, $model){
							if(!$model->isLocal or $model->isProdIssue)
								return false;
							if(!Yii::$app->user->can('admin')){
								if(!Yii::$app->user->can('mrp')){
									return false;
								}else{
									if(!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))
										return false;
								}
							}
							$url = Url::toRoute(['document/update-local', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-pencil text-warning" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Edit')
								]).'&nbsp;';
						},
						'delete-local' => function($url, $model){
							if(!$model->isLocal or $model->isProdIssue)
								return false;
							if(!Yii::$app->user->can('admin')){
								if(!Yii::$app->user->can('mrp')){
									return false;
								}else{
									if(!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))
										return false;
								}
							}
							$url = Url::toRoute(['document/delete-local', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-trash text-warning" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Delete'),
									'data' => [
										'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
										'method' => 'post',
									],
								]).'&nbsp;';
						},
						// local issue
						'update-local-issue' => function($url, $model){
							if(!$model->isLocalIssue or $model->isProdIssue)
								return false;
							if(!Yii::$app->user->can('admin')){
								if(!Yii::$app->user->can('mrp')){
									return false;
								}else{
									if(!in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds))
										return false;
								}
							}
							$url = Url::toRoute(['document/update-local-issue', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-pencil text-warning" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Edit')
								]).'&nbsp;';
						},
						'delete-local-issue' => function($url, $model){
							if(!$model->isLocalIssue or $model->isProdIssue)
								return false;
							if(!Yii::$app->user->can('admin')){
								if(!Yii::$app->user->can('mrp')){
									return false;
								}else{
									if(!in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds))
										return false;
								}
							}
							$url = Url::toRoute(['document/delete-local-issue', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-trash text-warning" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Delete'),
									'data' => [
										'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
										'method' => 'post',
									],
								]).'&nbsp;';
						},
						// local receipt kd
						'update-local-kd' => function($url, $model){
							if(!$model->isLocalkd or $model->isProdIssue)
								return false;
							if(!Yii::$app->user->can('admin')){
								if(!Yii::$app->user->can('mrp')){
									return false;
								}else{
									if(!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))
										return false;
								}
							}
							$url = Url::toRoute(['document/update-local-kd', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-pencil text-warning" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Edit')
								]).'&nbsp;';
						},
						'delete-local-kd' => function($url, $model){
							if(!$model->isLocalkd or $model->isProdIssue)
								return false;
							if(!Yii::$app->user->can('admin')){
								if(!Yii::$app->user->can('mrp')){
									return false;
								}else{
									if(!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))
										return false;
								}
							}
							$url = Url::toRoute(['document/delete-local-kd', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-trash text-warning" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Delete'),
									'data' => [
										'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
										'method' => 'post',
									],
								]).'&nbsp;';
						},
					],
				],
				[
					'attribute' => 'docnum',
					'filter' => false,
					'content' => function($model){
						return Html::a($model->docnum, Url::toRoute(['document/view', 'id' => $model->id]));
					},
				],
				[
					'attribute' => 'docdate',
					'content' => function($model){
						return date("d.m.Y", strtotime($model->docdate));
					},
				],
				[
					'attribute' => 'serial_number',
					'headerOptions' => ['style' => 'width: 200px;text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width: 200px;text-align: center;vertical-align:middle;'],
					'visible' => (in_array(Yii::$app->user->identity->roleName, ['mrpc','counter', 'admin', 'superadmin']))
				],
				[
					'attribute' => 'document_type_id',
					'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'content' => function($model){
						return $model->documentType->name;
					},
					'filter' => yii\helpers\ArrayHelper::map(DocumentType::find()->all(), 'id', 'name')
				],
				[
					'attribute' => 'from_warehouse_id',
					'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'content' => function($model){
						return $model->fromWarehouse->name;
					},
					'filter' => yii\helpers\ArrayHelper::map(Warehouse::find()->all(), 'id', 'name')
				],
				[
					'attribute' => 'to_warehouse_id',
					'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'content' => function($model){
						return $model->toWarehouse->name;
					},
					'filter' => yii\helpers\ArrayHelper::map(Warehouse::find()->all(), 'id', 'name'),
				],
				[
					'attribute' => 'action',
					'headerOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;color:#3c8dbc;'],
					'contentOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
					'content' => function($model){
						return '<b class="text-'.(($model->actionStatus == 1) ? 'aqua' : 'green').'">
                                '.$model->actionName.'
                            </b>';
					},
					'filter' => [1 => Yii::t('app', 'Receipt'), 0 => Yii::t('app', 'Issue')],
					'visible' => (Yii::$app->user->identity->roleName == 'mrp' || Yii::$app->user->identity->roleName == 'mrpc')
				],
				[
					'attribute' => 'adj',
					'header' => Yii::t('app', 'Adjust-t'),
					'headerOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;color:#3c8dbc;'],
					'contentOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
					'content' => function($model){
						if($model->document_type_id != 3)
							return false;
						return '<b class="text-'.(($model->from_warehouse_id == 99) ? 'aqua' : 'green').'">
                                '.$model->adjNameForList.'
                            </b>';
					},
					'filter' => [1 => Yii::t('app', 'Receipt'), 0 => Yii::t('app', 'Issue')],
					'visible' => (in_array(Yii::$app->user->identity->roleName, ['mfu', 'admin', 'superadmin']))
				],
				[
					'attribute' => 'status',
					'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					'content' => function($model){
						return '<strong class="txt-'.(($model->status == 1) ? 'primary' : 'warning').'">'.$model->statusName.'</strong>';
					},
					'filter' => [0 => Yii::t('app', 'Pending'), 1 => Yii::t('app', 'Confirmed')]
				],
			],
		]);
?>