<?php
	use app\assets\AdminLteAsset;
	use yii\helpers\Html;
	use yii\web\JqueryAsset;

	/* @var $this yii\web\View */
	/* @var $model app\models\Document */
	$this->title = $model->docnum.' ('.$model->docdate.')';
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Document'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>

<?
	$this->registerCssFile("@themes/css/print.css", [
		'depends' => [
			AdminLteAsset::className()
		]
	]);
	$this->registerCssFile("@themes/css/print2.css", [
		'depends' => [
			AdminLteAsset::className()
		]
	]);
?>
<div class="document-view">


	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"><?=Yii::t('app', 'Form 1')?></a></li>
			<li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false"><?=Yii::t('app', 'Form 2')?></a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="tab_1">

				<p class="pull-left">
					<?=Html::button(Yii::t('app', 'btn-pdf'), ['class' => 'btn btn-primary btn-sm  pull-right', 'id' => 'btnPdf', 'style' => 'margin-left:15px;'])?>
					<?=Html::button(Yii::t('app', 'btn-print'), ['class' => 'btn btn-info btn-sm  pull-right', 'id' => 'btnPrint'])?>
				</p>

				<div id="printarea" style="clear:both">
					<div class="panel">
						<div class="panel-body">
							<div>		
								<p><b><?=Yii::t('app', 'Document date')?>:</b> <?=date("d.m.Y", strtotime($model->docdate))?> г.</p>
								<p><b><?=Yii::t('app', 'Document number')?>:</b> <span id="docnum"><?=$model->docnum?></span></p>
								<p><b><?=Yii::t('app', 'Warehouse A')?>:</b> <?=$model->fromWarehouse->name?></p>
								<p><b><?=Yii::t('app', 'Warehouse B')?>:</b> <?=$model->toWarehouse->name?></p>
								<? if(!empty($model->supplier_id)){ ?>
									<p><b><?=Yii::t('app', 'Supplier')?>:</b> <?=$model->supplier->name?></p>
								<? } ?>
								<p><b><?=Yii::t('app', 'Comment')?>:</b> <?=$model->comment?></p>
							</div>
							<div style=" text-align: center" >
								<img style="width: 100px;" src="<?='data:image/png;base64, '.$model->generateQrcode()?>">
							</div>
							<table class="table table-bordered table-responsive table-document">
								<tr>
									<th class="text-center" style="width: 30px">№</th>
									<th style="width: 140px"><?=Yii::t('app', 'Detail')?></th>
									<th><?=Yii::t('app', 'Part name')?></th>
									<th class="text-right" style="width: 60px"><?=Yii::t('app', 'Q-ty')?></th>
									<th class="text-center" style="width: 60px"><?=Yii::t('app', 'Unit')?></th>
								</tr>
								<?
									$i = 0;
									foreach($model->documentDetails as $detail){
										?>
										<tr>
											<td class="text-center"><?=++$i?></td>
											<td><?=$detail->part->partinfo?></td>
											<td><?=$detail->part->part_name?></td>
											<td class="text-right"><?=number_format($detail->qty, 2, '.', ' ')?></td>
											<td class="text-center"><?=$detail->part->unit->unit_value?></td>
										</tr>

									<? } ?>

							</table>
							<div>
								<div class="pull-left">
									Сдал: ____________________ ________________________________<?//=$model->fromWarehouse->users[0]->fullname ?? null?>
								</div>
								<div class="pull-right">
									Принял: ____________________ ______________________________<?//=$model->toWarehouse->users[0]->fullname ?? null?>
								</div>
							</div>
							<p class="text-right" style="clear: both; font-size: 12px; margin-top: 55px;font-style: italic">
								<b><?=Yii::t('app', 'Printed at')?>:</b> <?=date("d.m.Y H:i")?></p>
						</div>
					</div>
				</div>
			</div>
			<!-- /.tab-pane -->
			<div class="tab-pane" id="tab_2">
				<p class="pull-left">
					<?=Html::button(Yii::t('app', 'btn-pdf'), ['class' => 'btn btn-primary btn-sm  pull-right', 'id' => 'btnPdf2', 'style' => 'margin-left:15px;'])?>
					<?=Html::button(Yii::t('app', 'btn-print'), ['class' => 'btn btn-info btn-sm  pull-right', 'id' => 'btnPrint2'])?>
				</p>

				<div id="printarea2" style="clear:both;">
					<div class="panel">
						<div class="panel-body panel-body-doc2">
							
								<div >		
									<p><b><?=Yii::t('app', 'Document date')?>:</b> <?=date("d.m.Y", strtotime($model->docdate))?> г.</p>
									<p><b><?=Yii::t('app', 'Document number')?>:</b> <span id="docnum"><?=$model->docnum?></span></p>
									<p><b><?=Yii::t('app', 'Warehouse A')?>:</b> <?=$model->fromWarehouse->name?></p>
									<p><b><?=Yii::t('app', 'Warehouse B')?>:</b> <?=$model->toWarehouse->name?></p>
									<? if(!empty($model->supplier_id)){ ?>
										<p><b><?=Yii::t('app', 'Supplier')?>:</b> <?=$model->supplier->name?></p>
									<? } ?>
									<p><b><?=Yii::t('app', 'Comment')?>:</b> <?=$model->comment?></p>
								</div>
								<div style=" text-align: center" >
									<img style="width: 100px;" src="<?='data:image/png;base64, '.$model->generateQrcode()?>">
								</div>
							

							<table class="table table-bordered table-responsive table-document2">
								<tr>
									<th class="text-center" style="width: 30px">№</th>
									<th><?=Yii::t('app', 'Detail')?></th>
									<th class="text-right" style="width: 50px"><?=Yii::t('app', 'Q-ty')?></th>
									<th class="text-center" style="width: 50px"><?=Yii::t('app', 'Unit')?></th>
								</tr>
								<?
									$i = 0;
									foreach($model->documentDetails as $detail){
										?>
										<tr>
											<td class="text-center"><?=++$i?></td>
											<td>
												<?=$detail->part->partinfo?><br>
												<span style="font-size: 8px !important;">
                                            <?
	                                            $part_name = substr($detail->part->part_name, 0, 20);
	                                            if(strlen($detail->part->part_name) > 20)
		                                            $part_name .= '...';
	                                            echo $part_name;
                                            ?>
                                            </span>
											</td>
											<td class="text-right"><?=number_format($detail->qty, 2, '.', '')?></td>
											<td class="text-center"><?=$detail->part->unit->unit_value?></td>
										</tr>

									<? } ?>

							</table>
							<div>
								<div style="margin-top: 15px;">
									Сдал: ____________________ ________________________________<?//=$model->fromWarehouse->users[0]->fullname ?? null?>
								</div>
								<div style="margin-top: 15px;">
									Принял: ____________________ ______________________________<?//=$model->toWarehouse->users[0]->fullname ?? null?>
								</div>
							</div>
							<p style="clear: both; font-size: 12px; margin-top: 10px;font-style: italic">
								<b><?=Yii::t('app', 'Printed at')?>:</b> <?=date("d.m.Y H:i")?></p>
								
						</div>
					</div>
				</div>
			</div>
			<!-- /.tab-pane -->

		</div>
		<!-- /.tab-content -->
	</div>


	<p>
		<?=Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
	</p>

</div>

<?
	$this->registerJsFile("@themes/js/html2pdf.bundle.min.js", [
		'depends' => [
			JqueryAsset::className()
		]
	]);
?>


<?
	$this->registerJsFile("@themes/js/printThis.js", ['depends' => [JqueryAsset::className()]]);
?>

<?
	$print_scirpt = <<< JS
$('#btnPrint2').on("click", function () {
      $('#printarea2').printThis({
			// base: "window.location",
			base: false,                // preserve the BASE tag or accept a string for the URL
			loadCSS: ["themes/adminlte/css/print2.css", "/themes/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css"],                // path to additional css file - use an array [] for multiple
			pageTitle: "",              // add title to print page
			debug: false,               // show the iframe for debugging
			importCSS: true,            // import parent page css
			importStyle: true,         // import style tags
			printContainer: true,       // print outer container/$.selector
			removeInline: false,        // remove inline styles from print elements
			removeInlineSelector: "*",  // custom selectors to filter inline styles. removeInline must be true
			printDelay: 333,              // variable print delay
			header: null,               // prefix to html
			footer: null,               // postfix to html
			formValues: true,           // preserve input/form values
			canvas: false,              // copy canvas content
			// doctypeString: null,        // enter a different doctype for older markup
			removeScripts: false,       // remove script tags from print content
			copyTagClasses: true,      // copy classes from the html & body tag
			beforePrintEvent: null,     // function for printEvent in iframe
			beforePrint: null,          // function called before iframe is filled
         afterPrint: null            // function called before iframe is removed
      });
    });
        
$('#btnPrint').on("click", function () {
      $('#printarea').printThis({
			// base: "window.location",
			base: false,                // preserve the BASE tag or accept a string for the URL
			loadCSS: ["themes/adminlte/css/print.css", "/themes/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css"],                // path to additional css file - use an array [] for multiple
			pageTitle: "",              // add title to print page
			debug: false,               // show the iframe for debugging
			importCSS: true,            // import parent page css
			importStyle: true,         // import style tags
			printContainer: true,       // print outer container/$.selector
			removeInline: false,        // remove inline styles from print elements
			removeInlineSelector: "*",  // custom selectors to filter inline styles. removeInline must be true
			printDelay: 333,              // variable print delay
			header: null,               // prefix to html
			footer: null,               // postfix to html
			formValues: true,           // preserve input/form values
			canvas: false,              // copy canvas content
			// doctypeString: null,        // enter a different doctype for older markup
			removeScripts: false,       // remove script tags from print content
			copyTagClasses: true,      // copy classes from the html & body tag
			beforePrintEvent: null,     // function for printEvent in iframe
			beforePrint: null,          // function called before iframe is filled
         afterPrint: null            // function called before iframe is removed
      });
    });       
JS;
	$this->registerJs($print_scirpt);
?>

