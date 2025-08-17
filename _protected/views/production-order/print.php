<?php
	use app\assets\AdminLteAsset;
	use app\models\ProductionOrder;
	use yii\helpers\Html;
	use yii\web\JqueryAsset;

	/* @var $this yii\web\View */
	/* @var $model app\models\ProductionOrder */
	if(isset($action) and $action != 'create'){
		$this->title = Yii::t('app', 'Print production order');
		$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Production order'), 'url' => ['index']];
		$this->params['breadcrumbs'][] = $this->title;
	}
?>
<?=Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-success btn-sm pull-right'])?>
<?=Html::button(Yii::t('app', 'btn-print'), ['class' => 'btn btn-info btn-sm pull-right margin-r-5', 'id' => 'QRCodePrint'])?>

<?
	$this->registerCssFile("@themes/css/qrcode_print.css", ['depends' => [AdminLteAsset::className()]]);
	$this->registerJsFile("@themes/js/printThis.js", ['depends' => [JqueryAsset::className()]]);
?>

<div id="print_qrcode_div">
	<? foreach($model as $model){
		if($model->is_label !== ProductionOrder::LABEL_ACTUAL){
			if($model->quantity > 0) echo $this->render('big-label', ['model' => $model]);
			else echo $this->render('small-label', ['model' => $model]);
		}
	} ?>
</div>

<?
	$print_scirpt = <<< JS
$('#QRCodePrint').on("click", function () {
      $('#print_qrcode_div').printThis({
			// base: "window.location",
			base: false,                // preserve the BASE tag or accept a string for the URL
			loadCSS: ["/themes/adminlte/css/qrcode_print.css"],                // path to additional css file - use an array [] for multiple
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



