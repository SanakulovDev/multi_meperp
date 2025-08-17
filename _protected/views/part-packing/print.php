<?php
use app\assets\AdminLteAsset;
use app\components\Helpers;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionOrder */
/** @var TYPE_NAME $copies */
/** @var TYPE_NAME $qty */
/** @var TYPE_NAME $unit */
/** @var TYPE_NAME $editedLastSeq */
$this->title = Yii::t('app', 'Labeling');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Part packings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Print');
?>

<?
$invNo = (isset($_POST['invNo'])) ? $_POST['invNo'] : null;
$invDT = (isset($_POST['invDT'])) ? $_POST['invDT'] : null;
$qty = Helpers::numberFormatRemoveZero($qty, 2, ".", "");
?>

<div class="part-packing-print">
  <? ActiveForm::begin() ?>
  <div class="row">
    <div class="col-xs-6 col-sm-3">
      <div class="form-group  input-group-sm">
        <label for="invNo"><?=Yii::t('app', 'Invoice no')?>: </label>
        <?=Html::input('text', 'invNo',
          $invNo,
          $options = [
            'class' => 'chng-val form-control input-sm',
            'placeholder' => Yii::t('app', 'Invoice no'),
            'id' => 'invNo'
          ])?>
      </div>
    </div>
    <div class="col-xs-6 col-sm-3">
      <div class="form-group  input-group-sm">
        <label for="invNo"><?=Yii::t('app', 'Invoice date')?>: </label>
        <?=DateTimePicker::widget(
          [
            'name' => 'invDT',
            'type' => DateTimePicker::TYPE_INPUT,
            'value' => null,
            'options' => [
              'value' => date('Y-m-d'),
              'id' => 'invDT',
              'class' => 'chng-val form-control input-sm',
              'autocomplete' => 'off',
              'placeholder' => Yii::t('app', 'Invoice date'),
            ],
            'pluginOptions' => [
              'autoclose' => true,
              'format' => 'yyyy.mm.dd',
              'startView' => 'month',
              'minView' => 'month',
              'maxView' => 'month',
            ]
          ]);?>

      </div>
    </div>
    <div class="col-xs-6 col-sm-3">
      <div class="form-group required  input-group-sm">
        <label for="copy"><?=Yii::t('app', 'Qty')?>: </label>
        <?=Html::input('number', 'qty',
          $qty,
          $options = [
            'min' => 1,
            'step' => "any",
            'class' => 'chng-val form-control input-sm',
            'id' => 'qty',
            'style' => 'width:100px'
          ])?>
      </div>
    </div>
    <div class="col-xs-6 col-sm-2">
      <div class="form-group required input-group-sm">
        <label for="copy"><?=Yii::t('app', 'Copies')?>: </label>
        <?=Html::input('number', 'copy',
          $copies,
          $options = [
            'min' => 1,
            'step' => 1,
            'class' => 'chng-val form-control input-sm',
            'id' => 'copy',
            'style' => 'width:80px'
          ])?>
      </div>
    </div>
    <div class="col-xs-12 col-sm-1">
      <div class="form-group pull-right btn-group-sm">
        <?=Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'w80 btn btn-default'])?>
        <?=Html::submitButton(Yii::t('app', 'View'), ['id' => 'btn-view', 'class' => 'w80 btn btn-warning'])?>
      </div>
    </div>
  </div>
  <?php ActiveForm::end(); ?>

  <? if($copies > 0) { ?>
    <hr class="hr_style1" style="margin-top:0px">
    <?=Html::button(Yii::t('app', 'btn-print'), ['class' => 'btn btn-info btn-sm pull-right margin-r-5', 'id' => 'QRCodePrint'])?>
    <?
    $this->registerCssFile("@themes/css/qrcode_print.css", ['depends' => [AdminLteAsset::className()]]);
    $this->registerJsFile("@themes/js/printThis.js", ['depends' => [JqueryAsset::className()]]);
    $this->registerCssFile("@themes/css/jquery-confirm.min.css", ['depends' => [AdminLteAsset::className()]]);
    $this->registerJsFile("@themes/js/jquery-confirm.min.js", ['depends' => [JqueryAsset::className()]]);
    $this->registerJsFile("@themes/js/jquery-qrcode.js", ['depends' => [JqueryAsset::className()]]);
    ?>
    <div id="print_qrcode_div" style="display:none">
      <?=$this->render(
        'label-print',
        ['model' => $model,
          'invNo' => $invNo,
          'invDT' => $invDT,
          'qty' => $qty,
          'unit' => $unit,
          'copies' => $copies,
          'editedLastSeq' => $editedLastSeq,
        ]
      );
      ?>
    </div>
  <? } ?>

</div>

<?
$errMesssage = Yii::t('app', 'Quantity and copy must be greater than zero');
$errTitle = Yii::t('app', 'Error !');
$print_scirpt = <<< JS
	var WaitDialog = $.dialog({
    title: false,
    cancelButton: true,
    confirmButton: false,
    backgroundDismiss: true,
    backgroundDismissAnimation: 'glow',
    closeIcon: false,
    columnClass: 'col-xs-6 col-xs-offset-3 col-sm-4 col-sm-offset-4 col-md-2 col-md-offset-5',
    content: '<img src="/img/loading.gif" style="width:100%;height:100%"/>',
  });

	$(document).ready(function() {			
		
		$('.chng-val').on("keyup change", function () {
			$('#QRCodePrint').remove();
			let qty = $('#qty').val();
			let copy = $('#copy').val();
			let hasErr = (qty > 0  && copy > 0) ? 0 : 1;
			if(hasErr==1){
				$('#btn-view').hide();
				$.alert({				
				      onOpen: function () {
				      	if(this.isOpen()){
				      		$('.jconfirm-open').remove() 
				      	}				      	
				      },			
               keyboardEnabled: true,
               draggable: true,
               columnClass: 'col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1',
               icon: 'fa fa-warning text-danger',
               title: "<span class='text-bold text-danger'>$errTitle</span>",
               content: "<div class='text-danger'>$errMesssage</div>",
        });
			}else{
				$('#btn-view').show();
			}
		});		
		
		$.when(
			$(".tbl_qrcode").each(function( index ) {
        var serialQRCode = $(this).find(".serialNo").children().text();
        $(this).find(".qrCodeImg").qrcode({			
          render: 'image',  // 'canvas', 'image' or 'div'
          minVersion: 1,    // min/max versions
          maxVersion: 40,
          ecLevel: 'H',     // error correction level 'L', 'M', 'Q' or 'H'			
          left: 0,          // offset in pixels
          top: 0,			
          size: 160,        // size in pixels			
          fill: '#000',     // code color or image element			
          background: null, // background color or image element			
          radius: 0,        // border radius			
          quiet: 0,         // quiet zone in modules			
          mSize: 0.1,       // position options
          mPosX: 0.5,
          mPosY: 0.5,
          text: serialQRCode,
          mode: 2,
          label: 'OynaERP',
          fontname:'sans',
          fontcolor: '#000'
        });			
      }) 
		).then( function(){
		WaitDialog.close();
		$("#print_qrcode_div").show();
		})
		
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
	});
JS;
$this->registerJs($print_scirpt, yii\web\View::POS_LOAD);
?>



