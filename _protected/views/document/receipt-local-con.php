<?php
  use yii\helpers\Html;
  use yii\helpers\Url;
  use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
  /* @var $model app\models\ProductionOrder */
  $this->title = Yii::t('app', 'Receipt from consignment suppliers (scanning)');
  $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Document'), 'url' => ['index']];
  $this->params['breadcrumbs'][] = $this->title;
?>
<style>
  .table-order-600{
    font-size:20px;
  }
  .part-name{
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
</style>


<div class="document-create">

  <div class="document-form">
    
    <span id="json_wh_list" style="display: none"><?=$json_wh_list?></span>
    <span id="json_sp_list" style="display: none"><?=$json_sp_list?></span>
    <span id="json_part_list" style="display: none"><?=$json_part_list?></span>
    <span id="json_suppwh_ids" style="display: none"><?=$json_suppwh_ids?></span>
    

    <?php $form = ActiveForm::begin(['id' => 'formBarcode',
    'validateOnSubmit' => false]);?>

    <div class="row">
      <div class="col-lg-10 col-sm-10">
        <?=$form->field($model, 'barcode')->textInput(['placeholder' => Yii::t('app', 'Scan barcode'), 'class' => 'form-control input-lg', 'id' => 'barcode', 'data-barcode-page' => 'receipt-local-con', 'style' => 'font-size: 25px;', 'autofocus' => true])->label(false)?>
        <?=$form->field($model, 'barCodeData')->hiddenInput(['id'=>'barCodeData'])->label(false)?>
      </div>
      <div class="col-lg-2 col-sm-2">
        <div class="form-group">
          <?=Html::submitButton(Yii::t('app', 'btn-ok'), ['class' => 'btn btn-success btn-lg','id' => 'submit'])?>
        </div>
      </div>
    </div>
    <?php ActiveForm::end();?>

    

  </div>
  <div class="alert alert-danger" id="error" style="display: none">
    <button type="button" class="close" aria-hidden="true" id="btnCloseErrors">×</button>
    <h4 id="errorTitle"></h4>
    <span id="contentError"></span>
  </div>
  
  <? if(is_array($errorlist) and count($errorlist) > 0){ ?>
				<div class="alert alert-danger alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<h4><i class="icon fa fa-ban"></i> <?=Yii::t('app', 'Correct the following errors.')?></h4>
					<?
						if(is_array($errorlist['details']) and count($errorlist['details']) > 0){
							foreach($errorlist['details'] as $key => $errList){
								if(!in_array($key, ['no_item'])){
									echo '<b>'.$key.' - строка :</b><br/>';
								}
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
				</div>
			<? } ?>

  <div class="panel panel-default panel-body">
    <div class="row">
      <div class="col-lg-4">
        <?=Yii::t('app', 'Warehouse A')?>: <b id="from-wh"><?=$barcodeData->whFrom ?? null?></b>
        <span id="from-wh-id" style="display: none"><?=$barcodeData->whFromId ?? null?></span>
      </div>
      <div class="col-lg-2">
        <?=Yii::t('app', 'Warehouse B')?>: <b id="to-wh"><?=$barcodeData->whTo?? null?></b>
        <span id="to-wh-id" style="display: none"><?=$barcodeData->whToId ?? null?></span>
      </div>
      <div class="col-lg-3">
        <?=Yii::t('app', 'Document date')?>: <b id="doc-data"><?=$model->docdate?></b>
      </div>
    </div>
  </div>

  <div class="panel panel-default panel-body">
    <div class="row">
      <div class="col-lg-8">

        <p class="lead pull-left"><?=Yii::t('app', 'Part list')?></p>
        <!-- <p class="pull-right text-success">
          <button type="button" class="btn btn-success btn-sm pull-right btnAddDetail " title="Добавить новую деталь (F2)">
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
					</button>
        </p> -->
        <div style="clear: both"></div>  
        <div class="table-responsive">
          <table class="table" id="detailTable" style="table-layout: fixed;">
            <tbody>
              <tr id="tr-head">
                <th style="width:60px;">#</th>
                <th style="width:20%"><?=Yii::t('app', 'Part number')?></th>
                <th style="width:40%;" ><?=Yii::t('app', 'Part name')?></th>
                <th style="text-align: right"><?=Yii::t('app', 'Quantity')?></th>
                <th style="width:20%;text-align: center"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span></th>
              </tr>
              <?if(isset($barcodeData->partList) and count($barcodeData->partList)>0){?>
                <?$i=0;foreach($barcodeData->partList as $part){$i++;?>
              <tr class="tr_item" data-barcode = "<?=$part->barcodeText?>" title = "<?=$part->barcodeText?>">
                    <th><input type="hidden" name="items[num][]" value="<?=$i?>"><?=$i?></th>
                    <td class="part"><?=$part->partNumber?></td>
                    <td class="part-name" ><?=$part->partName?></td>
                    <td style="text-align: right" class="qty"><?=$part->qty?></td>
                    <td style="text-align: center"><span class="glyphicon glyphicon-trash text-danger removeIcon" aria-hidden="true"></span></td>
                  </tr>
                <?}?>
              <?}?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
  <div style="display: none">
    <span id="err_title"><?=Yii::t('app', 'Correct the following errors.')?></span> 
    <span id="err_part_list"><?=Yii::t('app', 'You must select at least one part.')?></span> 
    <span id="err_from_wh"><?=Yii::t('app', 'You must select warehouse which issuing.')?></span> 
    <span id="err_to_wh"><?=Yii::t('app', 'You must select warehouse which receiving.')?></span> 
    <span id="duplicate_barcode"><?=Yii::t('app', 'This barcode is already scanned.')?></span> 
  </div>
</div>
