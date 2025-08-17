<?
	use yii\helpers\Html;

?>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<p>
					<?=Yii::t('app', 'Details')?>
					<button type="button" class="btn btn-success btn-sm pull-right" id="btnAddContDetail">
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
							if(!in_array($key, ['no_item', 'cant_delete']))
								echo '<b>'.$key.' - строка :</b><br/>';
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

			<table class="table" id="inv_cont_ship">
				<tr>
					<th>№</th>
					<th><?=Yii::t('app', 'Container')." №"?></th>
					<th><?=Yii::t('app', 'Ship-DT')?></th>
					<th style="width: 20px"><?=Yii::t('app', 'Action')?></th>
				</tr>
				<tr id="tr_template" style="display: none">
					<th scope="row" style="text-align:  center;vertical-align:  middle;">
						<input type="hidden" name="items[num][]" value=""></th>
					<td><?=Html::input('text', 'items[container][]', '', ['class' => 'form-control']);?></td>
					<td><?=Html::input('date', 'items[ship_dt][]', '', ['class' => 'form-control']);?></td>

					<td style="text-align: center;vertical-align: middle">
						<span class="glyphicon glyphicon-trash text-danger removeIcon" aria-hidden="true"></span>
					</td>
				</tr>

				<?
					if(count($items['container']) > 1){
						foreach($items['container'] as $key => $value){
							if($key == 0)
								continue; ?>
							<tr class="tr_item">
								<th scope="row" style="text-align:  center;vertical-align:  middle;">
									<input type="hidden" name="items[num][]" value="<?=$key?>"><?=$key?></th>
								<td><?=Html::input('text', 'items[container][]', $items['container'][$key], ['class' => 'form-control']);?></td>
								<td><?=Html::input('date', 'items[ship_dt][]', $items['ship_dt'][$key], ['class' => 'form-control']);?></td>
								<td style="text-align: center;vertical-align: middle">
                                         <span class="glyphicon glyphicon-trash text-danger removeIcon"
                                               aria-hidden="true"></span>
								</td>
							</tr>
						<? }
					}
				?>

			</table>

		</div>
	</div>
</div>
