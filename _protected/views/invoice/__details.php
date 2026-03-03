<?
	use app\models\Part;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;

?>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<p>
					<?=Yii::t('app', 'Details')?>
					<button type="button" class="btn btn-success btn-sm pull-right btnAddDetail">
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
					</button>
				</p>
			</div>
			<? if(count($errorlist) > 0){ ?>
				<div class="alert alert-danger alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<h4><i class="icon fa fa-ban"></i> <?=Yii::t('app', 'Correct the following errors.')?></h4>
					<?
						foreach($errorlist as $key => $errList){
							if(!in_array($key, ['no_item', 'cant_delete'])) echo '<b>'.$key.' - строка :</b><br/>';
							foreach($errList as $err){
								foreach($err as $e){
									echo ' - '.$e.'<br/>';
								}
							}
							echo "<br/>";
						}
					?>
				</div>
			<? } ?>

			<table class="table" id="detailTable">
				<tr>
					<th>№</th>
					<th><?=Yii::t('app', 'Detail')?></th>
					<th><?=Yii::t('app', 'Quantity')?></th>
					<th><?=Yii::t('app', 'Price')?></th>
					<th><?=Yii::t('app', 'Currency')?></th>
					<th><?=Yii::t('app', 'Remarks')?></th>
					<th style="width: 20px"><?=Yii::t('app', 'Action')?></th>
				</tr>
				<tr id="tr_template" style="display: none">
					<?=Html::input('hidden', 'items[id][]', '', ['class' => 'form-control']);?>
					<th scope="row" style="text-align:  center;vertical-align:  middle;">
						<input type="hidden" name="items[num][]" value=""></th>
					<td><?=Html::dropDownList('items[detail][]', null, ArrayHelper::map(Part::find()->orderBy(['part_no' => SORT_ASC])->all(), 'id', 'part_no'), ['class' => 'form-control detail_part', 'prompt' => Yii::t('app', 'Select...')]);?></td>
					<td><?=Html::input('text', 'items[qty][]', '', ['class' => 'form-control']);?></td>
					<td><?=Html::input('text', 'items[price][]', '', ['class' => 'form-control']);?></td>
					<td><?=Html::input('text', 'items[currency][]', '', ['class' => 'form-control']);?></td>
					<td><?=Html::input('text', 'items[remarks][]', '', ['class' => 'form-control']);?></td>

					<td style="text-align: center;vertical-align: middle">
						<span class="glyphicon glyphicon-trash text-danger removeIcon" aria-hidden="true"></span>
					</td>
				</tr>

				<?
					if(count($items['detail']) > 1){
						foreach($items['detail'] as $key => $value){
							if($key == 0) continue;
							?>
							<tr class="tr_item">
								<?=Html::input('hidden', 'items[id][]', $items['id'][$key], ['class' => 'form-control']);?>
								<th scope="row" style="text-align:  center;vertical-align:  middle;">
									<input type="hidden" name="items[num][]" value="<?=$key?>"><?=$key?></th>
								<td><?=Html::dropDownList('items[detail][]', $items['detail'][$key], ArrayHelper::map(Part::find()->all(), 'id', 'part_color'), ['class' => 'form-control detail_part', 'prompt' => Yii::t('app', 'Select...')]);?></td>
								<td><?=Html::input('text', 'items[qty][]', $items['qty'][$key], ['class' => 'form-control']);?></td>
								<td><?=Html::input('text', 'items[price][]', $items['price'][$key], ['class' => 'form-control']);?></td>
								<td><?=Html::input('text', 'items[currency][]', $items['currency'][$key], ['class' => 'form-control']);?></td>
								<td><?=Html::input('text', 'items[remarks][]', $items['remarks'][$key], ['class' => 'form-control']);?></td>


								<td style="text-align: center;vertical-align: middle">
									<span class="glyphicon glyphicon-trash text-danger removeIcon" aria-hidden="true"></span>
								</td>
							</tr>
							<?
						}
					}
				?>

			</table>
			<div class="panel-footer">
				<p>
					&nbsp;
					<button type="button" class="btn btn-success btn-sm pull-right btnAddDetail">
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
					</button>
				</p>
			</div>
		</div>
	</div>
</div>
