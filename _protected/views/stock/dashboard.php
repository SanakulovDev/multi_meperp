<?php

use app\components\Helpers;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Remaining GP');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php ob_start();?>

    .tbl-plan{
        width: 60%;
        border-collapse: collapse;
        overflow-y: scroll;
        
    }
    .tbl-plan tr td{
        border: 2px solid black;
    } 
    .tbl-plan tr td{
        padding: 2px 3px;
        white-space: nowrap;
    }

    .tr_head td{
        font-weight: bold;
        text-align: center;
        background-color: #b6e8ff;
        height: 30px;
    }
    thead {
        position: sticky;
        top: 0;
        background-color: #b6e8ff;
        text-align: center;
        font-weight: bold;  
    }

    .content-index{
        max-height: 75vh;
        overflow-y: scroll;
        display:flex;
        justify-content: center;
    }
<?php $this->registerCss(ob_get_clean());?>
    <div class="panel-heading">
        <p class="pull-right" style="margin: 0px">
            <!-- php html activedropdownlist -->
            <?=Html::a(Yii::t('app', 'btn-download'), ['download-dashboard'], ['class' => 'btn btn-info btn-sm', 'id' => 'btnDownload'])?>
        </p>
        <p class="pull-right">
            <?= Html::dropDownList('warehouse_id', $warehouse_id, $user_warehouses,['class'=>'form-control  warehouse_id'])?>
        </p>
        <div style="clear: both;"></div>
    </div>
<div class="content-index">
  <table class="tbl-plan tbl-first table" id="fix_table">
	<thead>
		<tr class="tr_head">
			<td style="width: 20px">№</td>
			<td style="width: 300px"><?=Yii::t('app', 'Remark')?></td>
			<td style="width: 200px"><?= Yii::t('app', 'Part No')?></td>
			<td style="width: 400px"><?= Yii::t('app', 'Calculation name')?></td>
            <td style="width: 200px"><?= Yii::t('app', 'Part color')?></td>
            <td style="width: 200px"><?= Yii::t('app', 'Qty')?>.(кг)</td>
		</tr>
		
	</thead>

	<tbody>
		<?php foreach ($stocks as $key => $item): ?>
            <tr>
                <td style="font-weight: bold;">
                    <?= $key + 1 ?>
                </td>
                <td>
                    <?= $item['remark'] ?>
                </td>
                <td>
                    <?= $item['part_no'] ?>
                </td>
                <td>
                    <?= $item['part_name'] ?>
                </td>
                <td>
                    <?= preg_replace('/\d+/', '', $item['part_color']) ?>
                </td>
                <td style="text-align: right;">
                    <?= number_format($item['qty'], 2, '.', ' ')*1; ?>
                </td>
            </tr>
        <?php endforeach; ?>
	</tbody>
  </table>

</div>
<?php ob_start();?>
$(function(){
    $('.warehouse_id').on('change', function(){
        var warehouse_id = $(this).val();
        window.location.href = '<?=Url::to(['stock/dashboard'])?>?warehouse_id='+warehouse_id;
    })
})

<?php $this->registerJs(ob_get_clean());?>