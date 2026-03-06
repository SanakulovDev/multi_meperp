<?php
/* @var $this View */
/* @var $content string */
use app\assets\AdminLteAsset;
use app\widgets\Alert;
use hiqdev\assets\flagiconcss\FlagIconCssAsset;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

AdminLteAsset::register($this);
FlagIconCssAsset::register($this);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Url::to('/img/Logo.svg')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
<head>
	<meta charset="<?=Yii::$app->charset?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="icon" href="<?=Yii::$app->homeUrl?>img/Logo.svg" sizes="32x32">
  <?=Html::csrfMetaTags()?>
	<title><?=Yii::$app->name?> - <?=Html::encode($this->title)?></title>
  <?php $this->head() ?>
	<!-- Google Font -->
	<!--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">-->
	<!--    <base href="--><?php //=Yii::getAlias('@themes')?><!--/" >-->
</head>


<body class="hold-transition skin-blue sidebar-mini fixed sidebar-collapse">
<?php $this->beginBody() ?>
<div class="wrapper">


  <?php require_once 'adminlte/_modal.php'; ?>
  <?php require_once 'adminlte/_modalEta.php'; ?>
  <?php require_once 'adminlte/_modalEtaV.php'; ?>
  <?php require_once 'adminlte/_modalPayment.php'; ?>
  <?php require_once 'adminlte/_header.php'; ?>
  <?php require_once 'adminlte/_left_side.php'; ?>


	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
      <?php if(isset($this->params['breadcrumbs'])) { ?>
				<h1>
          <?=$this->title?>
					<!--        <small>здесь вы можете управлять компаниями</small>-->
				</h1>
      <?php } ?>
			<!--      <ol class="breadcrumb">
							<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
							<li><a href="#">Examples</a></li>
							<li class="active">Blank page</li>
						</ol>-->

      <?=Breadcrumbs::widget(
        [
          'homeLink' => [
            'label' => '<i class="fa fa-home"></i>'.Yii::t('yii', 'Home'),
            'url' => Yii::$app->homeUrl,
            'encode' => false// Requested feature
          ],
          'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ])?>

		</section>

		<!-- Main content -->
		<section class="content">
      <?=Alert::widget()?>
			<!-- Default box -->


      <?php Modal::begin(
        [
          'options' => ['tabindex' => false],
          'header' => '<h4 id="modal_head"></h4>',
          'id' => 'modal',
          'size' => 'modal-lg',
          'footer' => '<div class="form-group pull-right">'.
            '<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">'.Yii::t('app', 'btn-cancel').'</button>'.
            '<button type="button" class="btn btn-sm btn-success modalFormSubmit">'.Yii::t('app', 'btn-save').'</button>'.
            '</div>'
        ]); ?>
			<div id="modalContent"></div>
			<div class='form-group' id='modalError'>
				<div class='help-block'></div>
			</div>
      <?php Modal::end(); ?>

      <?php Modal::begin(
        [
          'options' => ['tabindex' => false],
          'header' => '<h4>'.Yii::t('app', 'Delete').'</h4>',
          'id' => 'modalDelete',
          'size' => 'modal-md',
          'footer' => '<div class="form-group pull-right">'.
            '<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">'.Yii::t('app', 'btn-cancel').'</button>'.
            '<button type="button" class="btn btn-sm btn-danger modalFormDelete">'.Yii::t('app', 'btn-delete').'</button>'.
            '</div>'
        ]); ?>
			<div><?=Yii::t('app', 'Are you sure you want to delete this item?')?></div>
      <?php Modal::end(); ?>

      <?php if(in_array(Yii::$app->controller->id.'/'.Yii::$app->controller->action->id, ['report/index'])) { ?>
        <?=$content?>
      <?php } else { ?>
				<div class="box">
					<div class="box-body">
            <?=$content?>
					</div>
				</div>
      <?php } ?>
			<!-- /.box -->
		</section>
		<!-- /.content -->
		<div id="notify-modal"></div>
	</div>
	<!-- /.content-wrapper -->
  <?php require_once 'adminlte/_footer.php'; ?>

</div>

<?php
$pending_docs_count = (in_array(Yii::$app->user->identity->rolename, ['mrp'])) ? $pending_docs_count : 0;
$pending_docs_count = $pending_docs_count ?? 0;
$past_eta_data_count = $past_eta_data_count ?? 0;
$past_eta_data_count_v = $past_eta_data_count_v ?? 0;
$past_payment_count = count(Yii::$app->view->params['past_payments']) ?? 0;
$script = <<< JS

    var pending_docs_count = $pending_docs_count;
    var past_eta_data_count = $past_eta_data_count;
    var past_eta_data_count_v = $past_eta_data_count_v;
    var past_payment_count = $past_payment_count;

    $(document).ready(function () {
        $('.sidebar-menu').tree();
        
        if(pending_docs_count != 0){
          $('#modal-pending').modal('show');
        }

        if(past_eta_data_count != 0){
          $('#modal-eta').modal('show');
				}
				
        if(past_eta_data_count_v != 0){
          $('#modal-eta-v').modal('show');
        }
				
        if(past_payment_count != 0){
          $('#modal-payment').modal('show');
        }
				
    })

JS;
$this->registerJs($script, yii\web\View::POS_END);
?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
