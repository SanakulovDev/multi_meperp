<?php

use app\models\Part;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ProductSpecification */

$this->title = $model->code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product specification'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

function getColor($state) {
    if($state == Part::STATE_FINISHED) return ["color"=>"text-success", "label"=>"P", "text"=>Yii::t('app','Product')];
    if($state == Part::STATE_SEMI) return ["color"=>"text-primary", "label"=>"S", "text"=>Yii::t('app','Semi-finished')];
    return ["color"=>"text-black", "label"=>"C", "text"=>Yii::t('app','Component')];
}
function drawTree($data) {
    foreach($data as $row){
        extract(getColor($row['state']));
        $info = $row['part_no'];
        $uloc = $row['warehouse'];
        $qty = $row['usage_qty'];
        $unit = $row['unit_value'];
        $partName = $row['part_name'];
        $partColor = $row['part_color'];
        if($row['code']) $partName .= ' -> '.$row['code'].($row['description'] ? '('.$row['description'].')': '');
        echo "<li>";
        echo "<p style='float: left;margin-bottom: 0px'><span class='selected_part_state $color' title='$text'>$label</span></p>";
        $qtyMeter = isset($row['usage_qty_meter']) && $row['usage_qty_meter'] ? round($row['usage_qty_meter'], 4) . ' m' : '';
        $qtyDisplay = $qty . ' kg' . ($qtyMeter ? ' / ' . $qtyMeter : '');
        echo "<p style='float: left;margin-left: 10px;margin-bottom: 0px;min-width: 500px;'>";
        echo "<span class='modal-title selected_part_info $color'>$info</span> | <span class='selected_part_uloc'>$uloc</span> | <span class='selected_part_uloc'>$qtyDisplay</span> | <span class='selected_part_unit'>$unit</span><br>";
        echo "<span class='selected_part_name'>$partName - $partColor</span>";
        echo "</p><div style='clear: both'></div>";

        if(isset($row['child'])){
            echo "<ul>";
            echo drawTree($row['child']);
            echo "</ul>";
        }
        echo "</li>";
    }            
}

?>
<div class="product-specification-view">
    

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'code',
                'format' => 'html',
                'value' => function($model) {
                    $color = 'text-black';
                    switch ($model->part->state) {
                        case Part::STATE_FINISHED:
                            $color = 'text-success';
                            break;
                        case Part::STATE_SEMI:
                            $color = 'text-primary';
                            break;
                        }
                    return Html::a($model->code, '#', ['class' => $color]);
                }
            ],
            [
                'attribute'=>'part_id',
                'value' => $model->part->partinfo . ' ' .$model->part->part_name
            ],
            [
            'attribute' => 'amount',
                'value' => function($model) { return $model->amount + 0; }
            ],
            'description',
            [
                'attribute'=>'status',
                'value' => $model->statusList()[$model->status]
            ],
            [
                'attribute'=>'updated_by',
                'value' => $model->updatedBy->fullname
            ],
            [
                'attribute'=>'updated_at',
                'value' => $model->updatedAtFormatted
            ],
        ],
    ]) ?>
   
</div>

<div class="panel panel-default">
    <ul class="bom_treeview">
    <?php
       drawTree($tree);  
    ?>        
    </ul>
</div>

<p class="pull-right">
    <?= Html::a(Yii::t('app', 'btn-update'), ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
    <?= Html::a(Yii::t('app', 'btn-delete'), ['delete', 'id' => $model->id], [
        'class' => 'btn btn-sm btn-danger',
        'data' => [
            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
            'method' => 'post',
        ],
    ]) ?>
</p>
<?php
$script = <<< JS
    $(".bom_treeview").treeView();  
JS;
$this->registerJs($script);
