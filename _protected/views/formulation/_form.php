<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Part;
use app\models\Customer;

$parts_select = Part::find()->where("state <> 2")->all();

$stable = [];
$ttable = [];
$e = 0;
$q = 0;
if ($model->id) {
    foreach ($specificList as $value) {
        $stable[$e]['item'] = $value->item;
        $stable[$e]['min'] = $value->min;
        $stable[$e]['max'] = $value->max;
        $stable[$e]['result'] = $value->result;
        $e++;
    }

    foreach ($titleList as $value) {
        $ttable[$q]['part_id'] = $value->part_id;
        $ttable[$q]['std_value'] = $value->std_value;
        $ttable[$q]['actual_value'] = $value->actual_value;
        $q++;
    }
}

//  
/* @var $this yii\web\View */
/* @var $model app\models\Formulation */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
function fill_unit_select_box($parts_select)
{
    $output = '';
    foreach ($parts_select as $part) {
        echo $part->part_no . '<br>';
        $output .= '<option value="' . $part->part_no . ' ' . $part->part_color . '">'
            . $part->part_no . ' ' . $part->part_color . '</option>';
    }
    return $output;
}
?>


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
<div class="formulation-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'formulation_base_id')->dropDownList($list, ['prompt' => Yii::t('app', ' . . . '), 'class' => 'form-control select2', 'id' => 'f_b_id']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'amount')->textInput(['maxlength' => true, 'type' => 'number']) ?>
        </div>
        <div class="col-lg-3">
            <?
            $customers = Customer::find()->all();
            $customer_items = ArrayHelper::map($customers, 'id', 'name');
            $params = ['prompt' => '. . .', 'class' => 'form-control input-sm select2'];
            echo $form->field($model, 'customer_id')->dropDownList($customer_items, $params);
            ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'order_no')->textInput(['type' => 'number']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'ulock')->textInput(['type' => 'number']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'due_at')->textInput() ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'start_at')->textInput() ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'finish_at')->textInput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'act_rate')->textInput(['maxlength' => true, 'type' => 'number']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'grind')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <?= $form->field($model, 'packages')->textarea(['rows' => 6]) ?>
    <div class="teble-responsive">
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">
                <span>Items table</span>
            </legend>
            <table class="table table-bordered" style="width: 100%" id="formulation_specific_table">
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
                <tr class="temp">
                    <td contenteditable='true' class="Item"></td>
                    <td contenteditable='true' class="Min"></td>
                    <td contenteditable='true' class="Max"></td>
                    <td contenteditable='true' class="Result"></td>
                    <td><button type="button" name="remove_item" class="btn btn-danger btn-sm remove_item">
                            <span class="glyphicon glyphicon-minus"></span></button>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
    <br />
    <div class="teble-responsive">
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">
                <span>Items table</span>
            </legend>
            <table class="table table-bordered table" style="width: 100%" id="formulation_item_table">
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
                <tr class="temp">
                    <td contenteditable='true' class="Code"></td>
                    <td contenteditable='true' class="Lot_No"></td>
                    <td contenteditable='true' class="Usage"></td>
                    <td contenteditable='true' class="Order_usage"></td>
                    <td contenteditable='true' class="Actual_order_usage"></td>
                    <td><button type="button" name="remove_item" class="btn btn-danger btn-sm remove_item">
                            <span class="glyphicon glyphicon-minus"></span></button>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>

    <div class="form-group pull-right">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>

<?
$script1 = <<< JS

    $("#f_b_id").on('change', function() {
        $("#formulation_item_table").find('tbody tr').remove();
        $("#formulation_specific_table").find('tbody tr').remove();
        fb_id = $(this).val();
        sendId(fb_id);        
    })

    function sendId(id) {
        $.ajax({
            type: 'GET',
            url: '../formulation-base/info',
            data: {
                'fb_id': id        
                },
            success: function(data) {
                if(data) {
           console.log('data1: ', data)
                storeComponentTable(JSON.parse(data.items))
                storeSpecificationTable(JSON.parse(data.specifications))
                storeInstructions(data.instructions)
                }
            }
        });
    }

    $(document).ready(function() {        
        $('#formulation_specific_table .temp').remove();
        $('#formulation_item_table .temp').remove();
    });
JS;
$this->registerJs($script1);
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
    var id = <?php echo $model->id ? 1 : 0 ?>;
    //Populate formulation-component table by formulation-base when choosed part_id
    function storeComponentTable(data) {
        if (data.length) {
            for (var i = 0; i < data.length; i++) {
                $("#formulation_item_table").find('tbody:last').append('<tr>' +
                    '<td class="Code"><select ' +
                    'class="form-control item-unit select2" name="items[' + i + '][code]"><option selected="selected">' + data[i].Code +
                    '</option><?php echo fill_unit_select_box($parts_select); ?></select></td>' +
                    '<td contenteditable=true width="18%" class="Lot_No"><input name="items[' + i + '][lot_no]" type="text" class="lot_no form-control" value=' + data[i].Lot_No + ' readonly="readonly"></td>' +
                    '<td contenteditable=true width="18%" class="Usage"><input name="items[' + i + '][usage]" type="text" class="usage form-control" value=' + data[i].Usage + ' readonly="readonly"></td>' +
                    '<td contenteditable=true width="18%" class="Order_usage"><input name="items[' + i + '][std_value]" type="number" class="order_usage form-control" value=' + data[i].Order_usage + '></td>' +
                    '<td contenteditable=true width="18%" class="Actual_order_usage"><input name="items[' + i + '][actual_value]" type="number" class="actual_order_usage form-control" value=' + data[i].Actual_order_usage + '></td>' +
                    '<td width="60px"><button type="button" name="remove_item" class="btn btn-danger btn-sm remove_item">' +
                    '<span class="glyphicon glyphicon-minus"></span></button></td>' +
                    '</tr>');
            }
        }
    }

    //Populate formulation-specification table by formulation-base when choosed part_id
    function storeSpecificationTable(data) {
        if (data.length) {
            for (var i = 0; i < data.length; i++) {
                $("#formulation_specific_table").find('tbody:last').append('<tr>' +
                    '<td class="Item"><input type="text" class="item form-control" name=specs[' + i + '][item]" value=' + data[i].Item + '></td>' +
                    '<td width="23%" class="Min"><input type="number" class="min form-control" name=specs[' + i + '][min]" value=' + data[i].Min + '></td>' +
                    '<td width="23%" class="Max"><input type="number" class="max form-control" name=specs[' + i + '][max]" value=' + data[i].Max + '></td>' +
                    '<td width="23%" class="Result"><input type="number" class="result form-control" name=specs[' + i + '][result]" value=' + data[i].Result + '></td>' +
                    '<td width="60px"><button type="button" name="remove_item" class="btn btn-danger btn-sm remove_item">' +
                    '<span class="glyphicon glyphicon-minus"></span></button></td>' +
                    '</tr>');
            }
        }
    }

    //put inctructions by formulation-base when choosed part_id
    function storeInstructions(data) {
        if (data.length) {
            $('.instructions').val(data);
        }
    }

    if (id == 0) {
        $(document).ready(function() {
            //create.php
            //Add new row to formulation-component table when clicked Add button
            $(document).on('click', '.add_item', function() {
                var index = $("#formulation_item_table").find("tr").length - 1;
                var html = '';
                html += '<tr>';
                html += '<td class="Code"><select name="items[' + index + '][code]"' +
                    'class="form-control item-unit"><option>' +
                    '</option><?php echo fill_unit_select_box($parts_select); ?></select></td>';
                html += '<td class="Lot_No"><input type="text" name="items[' + index + '][lot_no]"' +
                    'class="form-control lot_no" readonly="readonly" /></td>';
                html += '<td class="Usage"><input type="text" name="items[' + index + '][usage]"' +
                    'class="form-control usage"  readonly="readonly"/></td>';
                html += '<td class="Order_usage"><input type="number" name="items[' + index + '][std_value]"' +
                    'class="form-control order_usage" /></td>';
                html += '<td class="Actual_order_usage"><input type="number" name="items[' + index + '][actual_value]"' +
                    'class="form-control actual_order_usage" /></td>';
                html += '<td><button type="button" name="remove_item" class="btn btn-danger btn-sm remove_item">' +
                    '<span class="glyphicon glyphicon-minus"></span></button></td></tr>'
                $('#formulation_item_table').append(html);
            });

            $(document).on('click', '.remove_item', function() {
                $(this).closest('tr').remove();
            });
            //create.php
            //Add new row to formulation-specification table when clicked Add button
            $(document).on('click', '.add_spec', function() {
                var index = $("#formulation_specific_table").find("tr").length - 1;
                var html = '';
                html += '<tr>';
                html += '<td class="Item"><input type="text" name="specs[' + index + '][item]"' +
                    'class="form-control item" /></td>';
                html += '<td class="Min"><input type="number" name="specs[' + index + '][min]"' +
                    'class="form-control min" /></td>';
                html += '<td class="Max"><input type="number" name="specs[' + index + '][max]"' +
                    'class="form-control max" /></td>';
                html += '<td class="Result"><input type="number" name="specs[' + index + '][result]"' +
                    'class="form-control result" /></td>';
                html += '<td><button type="button" name="remove_spec" class="btn btn-danger btn-sm remove_spec">' +
                    '<span class="glyphicon glyphicon-minus"></span></button></td></tr>'
                $('#formulation_specific_table').append(html);
            });

            $(document).on('click', '.remove_spec', function() {
                $(this).closest('tr').remove();
            });
        });
    } else {

        //update.php
        //Add new row to formulation-component table when clicked Add button
        $(document).ready(function() {
            $(document).on('click', '.add_item', function() {
                var index = $("#formulation_item_table").find("tr").length - 1;
                var html = '';
                html += '<tr>';
                html += '<td class="Code"><select name="items[' + index + '][code]"' +
                    'class="form-control item-unit"><option>' +
                    '</option><?php echo fill_unit_select_box($parts_select); ?></select></td>';
                html += '<td class="Lot_No"><input type="text" name="items[' + index + '][lot_no]"' +
                    'class="form-control lot_no" readonly="readonly" /></td>';
                html += '<td class="Usage"><input type="text" name="items[' + index + '][usage]"' +
                    'class="form-control usage" readonly="readonly" /></td>';
                html += '<td class="Order_usage"><input type="number" name="items[' + index + '][std_value]"' +
                    'class="form-control order_usage" /></td>';
                html += '<td class="Actual_order_usage"><input type="number" name="items[' + index + '][actual_value]"' +
                    'class="form-control actual_order_usage" /></td>';
                html += '<td><button type="button" name="remove_item" class="btn btn-danger btn-sm remove_item">' +
                    '<span class="glyphicon glyphicon-minus"></span></button></td></tr>'
                $('#formulation_item_table').find('tbody:last').append(html);
            });

            $(document).on('click', '.remove_item', function() {
                $(this).closest('tr').remove();
            });

            //create.php
            //Add new row to formulation-specification table when clicked Add button
            $(document).on('click', '.add_spec', function() {
                var index = $("#formulation_specific_table").find("tr").length - 1;
                var html = '';
                html += '<tr>';
                html += '<td class="Item"><input type="text" name="specs[' + index + '][item]"' +
                    'class="form-control item" /></td>';
                html += '<td class="Min"><input type="text" name="specs[' + index + '][min]"' +
                    'class="form-control min" /></td>';
                html += '<td class="Max"><input type="text" name="specs[' + index + '][max]"' +
                    'class="form-control max" /></td>';
                html += '<td class="Result"><input type="text" name="specs[' + index + '][result]"' +
                    'class="form-control result" /></td>';
                html += '<td><button type="button" name="remove_spec" class="btn btn-danger btn-sm remove_spec">' +
                    '<span class="glyphicon glyphicon-minus"></span></button></td></tr>'
                $('#formulation_specific_table').find('tbody:last').append(html);
            });

            $(document).on('click', '.remove_spec', function() {
                $(this).closest('tr').remove();
            });
        });
    }

    var spList = [];
    var tList = [];
    $(document).ready(function() {
        $(".submit").on('click', function(e) {
            //Parse formulation-component table
            $("#formulation_item_table > tbody > tr").each(function(index, element) {
                var i = $(this).index();
                var tObject = {};
                tObject['Code'] = $(element).find(".item-unit option:selected").text();
                tObject['Lot_No'] = $(element).find(".lot_no").val();
                tObject['Usage'] = $(element).find(".usage").val();
                tObject['Order_usage'] = $(element).find(".order_usage").val();
                tObject['Actual_order_usage'] = $(element).find(".actual_order_usage").val();
                tList[i] = tObject;
                tObject = {};
                $("#formulation_item_table").val(JSON.stringify(tList));
            });

            //Parse formulation-component table
            $("#formulation_specific_table > tbody > tr").each(function(index, element) {
                var i = $(this).index();
                var spObject = {};
                spObject['Item'] = $(element).find(".item").val();
                spObject['Min'] = $(element).find(".min").val();
                spObject['Max'] = $(element).find(".max").val();
                spObject['Result'] = $(element).find(".result").val();
                spList[i] = spObject;
                spObject = {};
                $("#formulation_specific_table").val(JSON.stringify(spList));
            });
            $.session.set('spList', JSON.stringify(spList));

        });

        // Update
        if (id == 1) {
            var itemList = <?php echo json_encode((object) $ttable) ?>;
            var count_it = <?php echo count($stable) ?>;
            if (itemList) {
                for (var i = 0; i < count_it; i++) {
                    $("#formulation_item_table").find('tbody:last').append('<tr>' +
                        '<td class="Code"><select ' +
                        'class="form-control item-unit select2" name="items[' + i + '][code]"><option selected="selected">' + itemList[i].part_id +
                        '</option><?php echo fill_unit_select_box($parts_select); ?></select></td>' +
                        '<td contenteditable=true width="18%" class="Lot_No"><input type="text" class="lot_no form-control" name="items[' + i + '][lot_no]" value="" readonly="readonly"></td>' +
                        '<td contenteditable=true width="18%" class="Usage"><input type="text" class="usage form-control" name="items[' + i + '][usage]" value="" readonly="readonly"></td>' +
                        '<td contenteditable=true width="18%" class="Order_usage"><input type="number" class="order_usage form-control" name="items[' + i + '][std_value]" value=' + itemList[i].std_value + '></td>' +
                        '<td contenteditable=true width="18%" class="Actual_order_usage"><input type="number" class="actual_order_usage form-control" name="items[' + i + '][actual_value]" value=' + itemList[i].actual_value + '></td>' +
                        '<td width="60px"><button type="button" name="remove_item" class="btn btn-danger btn-sm remove_item">' +
                        '<span class="glyphicon glyphicon-minus"></span></button></td>' +
                        '</tr>');
                }
            }

            var specificList = <?php echo json_encode((object) $stable) ?>;
            var count = <?php echo count($stable) ?>;
            if (specificList) {
                for (var i = 0; i < count; i++) {
                    $("#formulation_specific_table").find('tbody:last').append('<tr>' +
                        '<td class="Item"><input type="text" class="item form-control" name=specs[' + i + '][item]" value=' + specificList[i].item + '></td>' +
                        '<td width="23%" class="Min"><input type="number" class="min form-control" name=specs[' + i + '][min]" value=' + specificList[i].min + '></td>' +
                        '<td width="23%" class="Max"><input type="number" class="max form-control" name=specs[' + i + '][max]" value=' + specificList[i].max + '></td>' +
                        '<td width="23%" class="Result"><input type="number" class="result form-control" name=specs[' + i + '][result]" value=' + specificList[i].result + '></td>' +
                        '<td width="60px"><button type="button" name="remove_item" class="btn btn-danger btn-sm remove_item">' +
                        '<span class="glyphicon glyphicon-minus"></span></button></td>' +
                        '</tr>');
                }
            }
        }
    });
</script>