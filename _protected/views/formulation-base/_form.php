<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Part;

$parts = ArrayHelper::map(Part::find()->where("state <> 2")->all(), 'id', 'partinfo');
$parts_select = Part::find()->where("state <> 2")->all();


/* @var $this yii\web\View */
/* @var $model app\models\FormulationBase */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
function fill_unit_select_box($parts_select)
{
    $output = '';
    foreach ($parts_select as $part) {
        echo $part->part_no . '<br>';
        $output .= '<option value="' . $part->part_no . '">'
            . $part->part_no . ' ' . $part->part_color . '</option>';
    }
    return $output;
}
?>

<div class="formulation-base-form">
    <style>
        .instructions {
            display: block;
            margin: auto;
            font-size: 16px;
            width: 100%;
            padding: 10px 0;
            line-height: 33px;
            background-image: linear-gradient(#eee 1px, transparent 1px);
            background-size: 100% 37px;
            border: 1px solid lightgray;
            outline: 0;
        }
    </style>
    <?php //print_r($model)
    ?>
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'part_id')->dropDownList([ArrayHelper::map(Part::find()->where("state <> 0")->all(), 'id', 'partinfo')], ['prompt' => Yii::t('app', ' . . . '), 'class' => 'form-control select2']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'pack')->textInput(['maxlength' => true, 'type' => 'number']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'version')->textInput(['type' => 'number']) ?>
        </div>
        <div class="col-lg-3">
            <?php
            echo $form->field($model, 'status')->dropDownList([$model->statusList])
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'std_rate')->textInput(['maxlength' => true, 'type' => 'number']) ?>
        </div>
    </div>
    <?= $form->field($model, 'instructions')->textarea(['rows' => 5, 'class' => 'instructions']) ?>
    <div id="items-spec">
        <?= $form->field($model, 'specifications')->textarea(['rows' => 5, 'id' => 'fbs']) ?>
        <?= $form->field($model, 'items')->textarea(['rows' => 5, 'id' => 'fbi']) ?>
    </div>
    <div class="teble-responsive">
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">
                <span>Specification table</span>
            </legend>
            <table class="table table-bordered" style="width: 100%" id="specific_table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th width="23%">Min</th>
                        <th width="23%">Max</th>
                        <th width="23%">Result</th>
                        <th width="60px"><button type="button" name="add_spec" class="btn btn-success btn-sm add_spec">
                                <span class="glyphicon glyphicon-plus"></span></button>
                        </th>
                    </tr>
                </thead>
                <?php foreach ($specificList as $row) : ?>
                    <tr class="temp">
                        <td contenteditable='true' class="Item"><?php echo $row->Item; ?></td>
                        <td contenteditable='true' class="Min"><?php echo $row->Min; ?></td>
                        <td contenteditable='true' class="Max"><?php echo $row->Max; ?></td>
                        <td contenteditable='true' class="Result"><?php echo $row->Result; ?></td>
                        <td><button type="button" name="remove_item" class="btn btn-danger btn-sm remove_item">
                                <span class="glyphicon glyphicon-minus"></span></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </fieldset>
    </div>
    <br />

    <div class="teble-responsive">
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">
                <span>Items table</span>
            </legend>
            <table class="table table-bordered" style="width: 100%" id="item_table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th width="18%">Lot No</th>
                        <th width="18%">Usage</th>
                        <th width="18%">Order usage</th>
                        <th width="18%">Actual order usage</th>
                        <th width="60px"><button type="button" name="add_item" class="btn btn-success btn-sm add_item">
                                <span class="glyphicon glyphicon-plus"></span></button>
                        </th>
                    </tr>
                </thead>
                <?php foreach ($titleList as $row) : ?>
                    <tr class="temp">
                        <td contenteditable='true' class="Code"></td>
                        <td contenteditable='true' class="Lot_No"><?php echo $row->Lot_No; ?></td>
                        <td contenteditable='true' class="Usage"><?php echo $row->Usage; ?></td>
                        <td contenteditable='true' class="Order_usage"><?php echo $row->Order_usage; ?></td>
                        <td contenteditable='true' class="Actual_order_usage"><?php echo $row->Actual_order_usage; ?></td>
                        <td><button type="button" name="remove_item" class="btn btn-danger btn-sm remove_item">
                                <span class="glyphicon glyphicon-minus"></span></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </fieldset>
    </div>
    <div class="form-group pull-right">
        <?= Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm']) ?>
        <?= Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm submit btn-custom']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
    var id = <?php echo $model->id ? 1 : 0 ?>;

    if (id == 1) {
        var itemList = <?php echo json_encode($titleList) ?>;
        if (itemList.length) {
            for (var i = 0; i < itemList.length; i++) {
                $("#item_table").find('tbody:last').append('<tr>' +
                    '<td class="Code"><select name="item_unit[]"' +
                    'class="form-control item-unit"><option selected="selected" value="">' + itemList[i].Code +
                    '</option><?php echo fill_unit_select_box($parts_select); ?></select></td>' +
                    '<td contenteditable=true width="18%" class="Lot_No"><input type="text" class="lot_no form-control" value=' + itemList[i].Lot_No + '></td>' +
                    '<td contenteditable=true width="18%" class="Usage"><input type="text" class="usage form-control" value=' + itemList[i].Usage + '></td>' +
                    '<td contenteditable=true width="18%" class="Order_usage"><input type="text" class="order_usage form-control" value=' + itemList[i].Order_usage + '></td>' +
                    '<td contenteditable=true width="18%" class="Actual_order_usage"><input type="text" class="actual_order_usage form-control" value=' + itemList[i].Actual_order_usage + '></td>' +
                    '<td width="60px"><button type="button" name="remove_item" class="btn btn-danger btn-sm remove_item">' +
                    '<span class="glyphicon glyphicon-minus"></span></button></td>' +
                    '</tr>');
            }
        }

        var specificList = <?php echo json_encode($specificList) ?>;

        if (specificList.length) {
            for (var i = 0; i < specificList.length; i++) {
                $("#specific_table").find('tbody:last').append('<tr>' +
                    '<td class="Item"><input type="text" class="item form-control" value=' + specificList[i].Item + '></td>' +
                    '<td width="23%" class="Min"><input type="text" class="min form-control" value=' + specificList[i].Min + '></td>' +
                    '<td width="23%" class="Max"><input type="text" class="max form-control" value=' + specificList[i].Max + '></td>' +
                    '<td width="23%" class="Result"><input type="text" class="result form-control" value=' + specificList[i].Result + '></td>' +
                    '<td width="60px"><button type="button" name="remove_item" class="btn btn-danger btn-sm remove_item">' +
                    '<span class="glyphicon glyphicon-minus"></span></button></td>' +
                    '</tr>');
            }
        }
    }

    if (id == 0) {
        $(document).ready(function() {
            $('#specific_table .temp').remove();
            $('#item_table .temp').remove();
        })
    } else {
        $(document).ready(function() {
            $('#specific_table .temp').remove();
            $('#item_table .temp').remove();
        })
    }

    if (id == 0) {
        $(document).ready(function() {
            $(document).on('click', '.add_item', function() {
                var html = '';
                html += '<tr>';
                html += '<td class="Code"><select name="item_unit[]"' +
                    'class="form-control item-unit"><option value="">' +
                    '</option><?php echo fill_unit_select_box($parts_select); ?></select></td>';
                html += '<td class="Lot_No"><input type="text" name="lot_no[]"' +
                    'class="form-control lot_no" /></td>';
                html += '<td class="Usage"><input type="text" name="usage[]"' +
                    'class="form-control usage" /></td>';
                html += '<td class="Order_usage"><input type="text" name="order_usage[]"' +
                    'class="form-control order_usage" /></td>';
                html += '<td class="Actual_order_usage"><input type="text" name="actual_order_usage[]"' +
                    'class="form-control actual_order_usage" /></td>';
                html += '<td><button type="button" name="remove_item" class="btn btn-danger btn-sm remove_item">' +
                    '<span class="glyphicon glyphicon-minus"></span></button></td></tr>'
                $('#item_table').append(html);
            });

            $(document).on('click', '.remove_item', function() {
                $(this).closest('tr').remove();
            });


            $(document).on('click', '.add_spec', function() {
                var html = '';
                html += '<tr>';
                html += '<td class="Item"><input type="text" name="item[]"' +
                    'class="form-control item" /></td>';
                html += '<td class="Min"><input type="text" name="min[]"' +
                    'class="form-control min" /></td>';
                html += '<td class="Max"><input type="text" name="max[]"' +
                    'class="form-control max" /></td>';
                html += '<td class="Result"><input type="text" name="result[]"' +
                    'class="form-control result" /></td>';
                html += '<td><button type="button" name="remove_spec" class="btn btn-danger btn-sm remove_spec">' +
                    '<span class="glyphicon glyphicon-minus"></span></button></td></tr>'
                $('#specific_table').append(html);
            });

            $(document).on('click', '.remove_spec', function() {
                $(this).closest('tr').remove();
            });
        });
    } else {

        $(document).ready(function() {
            $(document).on('click', '.add_item', function() {
                var html = '';
                html += '<tr>';
                html += '<td class="Code"><select name="item_unit[]"' +
                    'class="form-control item-unit"><option value="">' +
                    '</option><?php echo fill_unit_select_box($parts_select); ?></select></td>';
                html += '<td class="Lot_No"><input type="text" name="lot_no[]"' +
                    'class="form-control lot_no" /></td>';
                html += '<td class="Usage"><input type="text" name="usage[]"' +
                    'class="form-control usage" /></td>';
                html += '<td class="Order_usage"><input type="text" name="order_usage[]"' +
                    'class="form-control order_usage" /></td>';
                html += '<td class="Actual_order_usage"><input type="text" name="actual_order_usage[]"' +
                    'class="form-control actual_order_usage" /></td>';
                html += '<td><button type="button" name="remove_item" class="btn btn-danger btn-sm remove_item">' +
                    '<span class="glyphicon glyphicon-minus"></span></button></td></tr>'
                $('#item_table').find('tbody:last').append(html);
            });

            $(document).on('click', '.remove_item', function() {
                $(this).closest('tr').remove();
            });


            $(document).on('click', '.add_spec', function() {
                var html = '';
                html += '<tr>';
                html += '<td class="Item"><input type="text" name="item[]"' +
                    'class="form-control item" /></td>';
                html += '<td class="Min"><input type="text" name="min[]"' +
                    'class="form-control min" /></td>';
                html += '<td class="Max"><input type="text" name="max[]"' +
                    'class="form-control max" /></td>';
                html += '<td class="Result"><input type="text" name="result[]"' +
                    'class="form-control result" /></td>';
                html += '<td><button type="button" name="remove_spec" class="btn btn-danger btn-sm remove_spec">' +
                    '<span class="glyphicon glyphicon-minus"></span></button></td></tr>'
                $('#specific_table').find('tbody:last').append(html);
            });

            $(document).on('click', '.remove_spec', function() {
                $(this).closest('tr').remove();
            });
        });
    }
</script>