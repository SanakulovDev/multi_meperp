<?php

use cetver\LanguageSelector\items\DropDownLanguageItem;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\ReportGroup;
$url  = Yii::$app->request->url;
// vd($url);
$reportGroups = [];
$reportGroupsT = [];
$hiddenReports = [];

if($url == '/report/index'){
  	$query = ReportGroup::find();
  	$query->joinWith([
	  "reports" => function($query) {
		  $query->onCondition(["report.action" => ArrayHelper::map(Yii::$app->user->identity->reports, "id", "action")]);
		},
    ]);
    $reportGroupsT = $query->orderBy(["order" => SORT_ASC])->all();
    $reportGroups = [];
    foreach($reportGroupsT as $key => $rg) {
      if($rg->id == 6) {
        continue;
      } // Visitors
      if(count($rg->reports) == 0) {
		  continue;
		}
		$reportGroups[] = $rg;
    }
	$hiddenReports = [

		'vehicle-set',
		'pipeline',
		'import',
		'sp',
		'imos',
		'sum-imos',
	  
		'pfep-parts',
		'doh-calc',
	  
		'production-count-line',
		'ftq-by-line',
		'ftq-summary',
	  
		'coverage-balance-old',
		'cash-requirement',
	  
		'ccu',
		'ccum',
		
	  ];
}
?>
<?php ob_start();?>
	.dropdown-menu .report-item:hover{
		//background-color: #e0dcdc;
	}
	.report-item {
		display: block;
		width: 100%;
		padding: 0.25rem 1.5rem;
		clear: both;
		font-weight: 400;
		color: #212529;
		text-align: inherit;
		white-space: nowrap;
		background-color: transparent;
		border: 0;
	}


<?php $this->registerCss(ob_get_clean());?>
<header class="main-header">
	<a href="<?= Yii::$app->homeUrl ?>" class="logo">
		<!-- mini logo for sidebar mini 50x50 pixels -->
		<span class="logo-mini" title="<?= Yii::$app->params['comp_name'] ?>"><b><?= Yii::$app->params['comp_short_name'] ?></b></span>
		<!-- logo for regular state and mobile devices -->
		<span class="logo-lg" title="<?= Yii::$app->params['comp_name'] ?>"><b>MEP ERP</b></span>
	</a>
	<nav class="navbar navbar-static-top">
		<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</a>
		<?php if($url == '/report/index'):?>
			<div class="navbar-custom-menu" style="float:left!important; margin-left: 100px; margin-top: 10px;">
				<ul class="navbar-nav ml-auto">
					<?php foreach ($reportGroups as $key => $gr):?>
						<?php  
						// if($key > 2) continue;
							// if(count($gr->reports) == 0) continue;
							// if($gr->id == 7) continue;
						?>
						<li class="nav-item dropdown " style="margin-right: 30px; list-style: none; font-weight:bold;">
							<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: #fff; padding:0;">
							<?=Yii::t('app', $gr->name);?> 
							<i class="fa fa-chevron-down"></i>
							</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdown">
							<?php foreach ($gr->reports as $key => $report):?>
								<?php if(in_array($report->title, $hiddenReports)) continue;?>
								<a class="dropdown-item report-item" href="<?=Url::toRoute(['report/'.$report->action])?>"><?=Yii::t('app', $report->title);?></a>

							<?php endforeach;?>
							</div>
						</li>
					<?php endforeach;?>
						
				</ul>
			</div>
		<?php endif;?>
		<div class="navbar-custom-menu">

			<ul class="nav navbar-nav">

				<?php if (Yii::$app->user->can('past-payment-note')) { ?>

					<li class="dropdown notifications-menu">
						
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							<i class="fa fa-dollar"></i>
							<?php if (count(Yii::$app->view->params['past_payments']) > 0) {?>
							<span class="label label-primary" style="top: 4px;right: -2px;"><?= count(Yii::$app->view->params['past_payments']) ?></span>
							<?php }?>
						</a>

						<ul class="dropdown-menu">
								<?php if (count(Yii::$app->view->params['past_payments']) > 0) {?>
									<li class="header"><?= Yii::t('app', 'You have <b>{cnt}</b> past payments', ['cnt' => count(Yii::$app->view->params['past_payments'])]) ?> </li>
									<li>
										<!-- inner menu: contains the actual data -->
										<ul class="menu">
											<?php foreach (Yii::$app->view->params['past_payments'] as $row) {?>
											<li>
												<a href="<?= yii\helpers\Url::toRoute(['payment-control/index','PaymentControlSearch[dummy_order]' => 1]) ?>">
													<b><i class="fa fa-warning text-danger"></i> <?= $row->no ?></b> (<?= $row->date ?>)
												</a>
											</li>
											<?php }?>
										</ul>
									</li>
									<li class="footer" style="padding: 0px;height:0px;border-top:0px"><a href="<?= yii\helpers\Url::toRoute(['payment-control/index']) ?>"><?= Yii::t('app', 'View all') ?></a></li>
								<?php } else {?>
									<li class="header"><?= Yii::t('app', 'There is no past payments') ?></li>
								<?php }?>
						</ul>

					</li>
				
				<?php }?>


				<?php if (Yii::$app->user->can('vehicle-coverage-input-etadate-note')) {?>

				<li class="dropdown notifications-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<i class="fa fa-car"></i>
						<?php if ($past_eta_data_count_v > 0) {?>
						<span class="label label-warning" style="top: 4px;right: -2px;"><?= $past_eta_data_count_v ?></span>
						<?php }?>
					</a>



					<ul class="dropdown-menu">
						<?php if (Yii::$app->user->can('vehicle-coverage-input-etadate-note')) {?>
						<?php if ($past_eta_data_count_v>0) {?>
						<li class="header"><?= Yii::t('app', 'You have <b>{cnt}</b> vehicle sets with incorrect ETA date', ['cnt' => $past_eta_data_count_v]) ?> </li>
						<li>
							<!-- inner menu: contains the actual data -->
							<ul class="menu">
								<?php foreach ($past_eta_data_v as $row) {?>
								<li>
									<a href="<?= yii\helpers\Url::toRoute(['vehicle-coverage-input/index']) ?>">
										<b><i class="fa fa-warning text-danger"></i> <?= $row->model->description ?></b> (<?= $row->for_date ?>) - <?= number_format($row->quantity, 0, '', ' ') ?? 0 ?>
									</a>
								</li>
								<?php }?>
							</ul>
						</li>
						<li class="footer" style="padding: 0px;height:0px;border-top:0px"><a href="<?= yii\helpers\Url::toRoute(['vehicle-coverage-input/index']) ?>"><?= Yii::t('app', 'View all') ?></a></li>
						<?php } else {?>
						<li class="header"><?= Yii::t('app', 'There is no passed ETA dates') ?></li>
						<?php }?>
					</ul>
				</li>

				<?php }?>
				<?php }?>


				<?php if (Yii::$app->user->can('container-invoice-etadate-note')) {?>

				<li class="dropdown notifications-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<i class="fa fa-flag-o"></i>
						<?php if ($past_eta_data_count > 0) {?>
						<span class="label label-warning" style="top: 4px;right: -2px;"><?= $past_eta_data_count ?></span>
						<?php }?>
					</a>



					<ul class="dropdown-menu">
						<?php if (Yii::$app->user->can('container-invoice-etadate-note')) {?>
						<?php if ($past_eta_data_count>0) {?>
						<li class="header"><?= Yii::t('app', 'You have <b>{cnt}</b> invoices with incorrect ETA date', ['cnt' => $past_eta_data_count]) ?> </li>
						<li>
							<!-- inner menu: contains the actual data -->
							<ul class="menu">
								<?php $ids = [];?>
								<?php foreach ($past_eta_data as $row) {?>
								<?php  $ids[] = $row->id ?>
								<li>
									<a href="<?= \yii\helpers\Url::toRoute(['container-invoice/view', 'id' => $row->id]) ?>">
										<b><i class="fa fa-warning text-danger"></i> <?= $row->invoice->invoice_no ?></b><br>
										<small class="text-black-50"><?= $row->container->container_no ?> (<?= $row->app_arr_at ?>)</small>
									</a>
								</li>
								<?php }?>
							</ul>
						</li>
						<li class="footer" style="padding: 0px;height:0px;border-top:0px"><a href="<?= yii\helpers\Url::toRoute(['container-invoice/index', 'ContainerInvoiceSearch[ids]' => $ids]) ?>"><?= Yii::t('app', 'View all') ?></a></li>
						<?php } else {?>
						<li class="header"><?= Yii::t('app', 'There is no passed ETA dates') ?></li>
						<?php }?>
					</ul>
				</li>

				<?php }?>
				<?php }?>





				<?php if (Yii::$app->user->can('document-pending-alert')) {?>
				<li class="dropdown notifications-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-bell-o"></i>
						<?php if ($pending_docs_count>0) {?>
						<span class="label label-danger" style="top: 4px;right: -2px;"><?= $pending_docs_count ?></span>
						<?php }?>
					</a>
					<ul class="dropdown-menu">
						<?php if ($pending_docs_count>0) {?>
						<li class="header"><?= Yii::t('app', 'You have <b>{cnt}</b> unconfirmed documents', ['cnt' => $pending_docs_count]) ?> </li>
						<li>
							<!-- inner menu: contains the actual data -->
							<ul class="menu">
								<?php foreach ($pending_docs as $pdoc) {?>
								<li>
									<a href="<?= \yii\helpers\Url::toRoute(['document/view', 'id' => $pdoc->id]) ?>">
										<b><i class="fa fa-warning text-danger"></i> <?= $pdoc->toWarehouse->name ?></b><br>
										<small class="text-black-50"><?= $pdoc->docnum ?> (<?= $pdoc->docdate ?>)</small>
									</a>
								</li>
								<?php }?>
							</ul>
						</li>
						<?php
              $route_params = [
                'document/index',
                'DocumentSearch[status]'=>0
              ];
              if (Yii::$app->user->identity->rolename == 'mrp' || Yii::$app->user->identity->rolename == 'mrpc') {
                  $route_params = array_merge($route_params, ['DocumentSearch[to_warehouse_id]'=> implode(',', Yii::$app->user->identity->warehouseIds)]);
              }
              ?>
						<li class="footer" style="padding: 0px;height:0px;border-top:0px"><a href="<?= yii\helpers\Url::toRoute($route_params) ?>"><?= Yii::t('app', 'View all') ?></a></li>
						<?php } else {?>
						<li class="header"><?= Yii::t('app', 'There is no unconfirmed documents') ?></li>
						<?php }?>
					</ul>
				</li>
				<?php }?>




				<li>

					<div class=" lng-menu pull-right">
						<?php
                    $languageItems = new DropDownLanguageItem(
                  [
                            'languages' => [
                                'en' => '<i class="flag-icon flag-icon-us"></i><span title="English"> English</span>',
                                'ru' => '<i class="flag-icon flag-icon-ru"></i><span title="Русский"> Русский</span>',
                                'uz' => '<i class="flag-icon flag-icon-uz"></i><span title="O`zbekcha"> O`zbekcha</span>',
                            ],
                            'options' => ['encode' => false, 'class' => 'lng-menu'],
                        ]
              );
                    $languageItem = $languageItems->toArray();
                    $languageDropdownItems = ArrayHelper::remove($languageItem, 'items');
                    //				echo "<pre>1:"; print_r($languageDropdownItems);echo "</pre>";
                    echo ButtonDropdown::widget(
                        [
                            'label' => $languageItem['label'],
                            'encodeLabel' => false,
                            'options' => [
                                'class' => 'btn-sml btn-link',
                                'style' => ''
                                //							'visible' => true,
                            ],
                            'dropdown' => [
                                'items' => $languageDropdownItems
                            ]
                        ]
                    ); ?>


					</div>

				</li>

				<li class="dropdown">
					<a href="javascript:void(0);" onclick="introJs().start();" class="btn" title="<?= Yii::t('intro', 'User guide') ?>">
						<i class="fa fa-question-circle"></i>
					</a>
				</li>
				<?php if (!Yii::$app->user->isGuest) { ?>
				<li class="dropdown">
					<a href="<?= yii\helpers\Url::toRoute('/site/logout') ?>" class="btn btn-flat" data-method="post"><i class="fa fa-sign-out"></i> <?= Yii::t('app', 'Logout') ?>
					</a>
				</li>
				<?php } ?>
			</ul>



		</div>



	</nav>
</header>


<?php ob_start();?>
$(function(){
	$('.dropdown-toggle').hover(function() {
		//$(this).parent().toggleClass('open');
	});

})
<?php $this->registerJs(ob_get_clean());?>