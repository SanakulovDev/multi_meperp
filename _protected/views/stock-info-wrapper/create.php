<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\StockInfoWrapper */

$this->title = Yii::t('app', 'Stock Info');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock Info'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
// vd($errorlist);
?>
<div class="stock-info-wrapper-create">

<? if(is_array($errorlist)  && ((isset($errorlist['details']) && !empty($errorlist['details'])) ||  (isset($errorlist['stock']) && !empty($errorlist['stock'])))){ ?>
				<div class="alert alert-danger alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<h4><i class="icon fa fa-ban"></i> <?=Yii::t('app', 'Correct the following errors.')?></h4>
					<?
						if(is_array($errorlist['details']) and count($errorlist['details']) > 0){
							foreach($errorlist['details'] as $key => $errList){
								foreach($errList as $err){
									foreach($err as $e){
										echo ' - '.$e.'<br/>';
									}
								}
								echo "<br/>";
							}
						}
					?>

					<?
						if(is_array($errorlist['stock'] ?? null) and count($errorlist['stock'] ?? null) > 0){
							echo '<b>'.Yii::t('app', 'No enough stock!').'</b><br/>';
							foreach($errorlist['stock'] as $key => $err){
								echo ' - '.$err.'<br/>';
							}
						}
					?>
					<?
						if(isset($errorlist['stock_receipt'])){
							if(is_array($errorlist['stock_receipt']) and count($errorlist['stock_receipt']) > 0){
								echo '<b>'.Yii::t('app', 'Stock receipt errors!').'</b><br/>';
								foreach($errorlist['stock_receipt'] as $key => $err){
									echo ' - '.$err.'<br/>';
								}
							}
						}
					?>
					<?
						if(isset($errorlist['stock_issue'])){
							if(is_array($errorlist['stock_issue']) and count($errorlist['stock_issue']) > 0){
								echo '<b>'.Yii::t('app', 'Stock issue errors!').'</b><br/>';
								foreach($errorlist['stock_issue'] as $key => $err){
									echo ' - '.$err.'<br/>';
								}
							}
						}
					?>
				</div>
			<? } ?>
    <?= $this->render('_form', [
        'model' => $model,
        'parts' => $parts,
        'parts_withptnm' => $parts_withptnm,
        'options' => $options,
        'models' => $models,
        'flocs' => $flocs,
    ]) ?>

</div>
