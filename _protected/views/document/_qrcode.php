<?
    
	use app\assets\AdminLteAsset;
    use yii\web\JqueryAsset;
    
?>
<div class="box box-success box-solid">
    <div class="box-header with-border">
        <h3 class="box-title"><?=Yii::t('app', 'Barcode')?></h3>

        <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool"  id="btn-print-qrcode" title="<?=Yii::t('app', 'Print barcode')?>"><i class="fa fa-print"></i></button> 
        
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            
        
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body text-center">
        <div id="div-qrcode">
            <div class="text-center text-bold">
                <?=$model->docnum?> / <?=$model->docdate?>
            <br>
                <img class="qrcode4_4" src="<?='data:image/png;base64, '.$model->generateQrcode()?>">
            </div>
        </div>
    </div>
    <!-- /.box-body -->
</div>

<br>
                
<?
	$this->registerJsFile("@themes/js/printThis.js", ['depends' => [JqueryAsset::className()]]);
?>

<?
	$print_scirpt = <<< JS
$('#btn-print-qrcode').on("click", function () {
      $('#div-qrcode').printThis({
			// base: "window.location",
			base: false,                // preserve the BASE tag or accept a string for the URL
			//loadCSS: ["/themes/adminlte/css/qrcode_print.css"],                // path to additional css file - use an array [] for multiple
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