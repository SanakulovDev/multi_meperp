<?php
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Pack */
/* @var $form yii\widgets\ActiveForm */
/* @var $permissionData app\controllers\AuthItemController */
$validationUrl = ["validate"];
if (!$model->isNewRecord) {
  $validationUrl["id"] = $model->name;
}
$form = ActiveForm::begin([
  "id" => $model->formName(),
  "enableAjaxValidation" => true,
  "validateOnType" => false,
  "validationUrl" => $validationUrl,
  "options" => ["data-pjax" => true, "class" => "modalForm"],
]);
?>
<style>
	.transfer-double{
		width:100%;
	}
	.transfer-demo{
		height:400px;
		margin:0 auto;
	}
</style>

<div class="row">
	<div class="col-sm-6 col-md-6 col-lg-6">
    <?= $form->field($model, "name")->textInput(["maxlength" => true, "disabled" => !$model->isNewRecord]) ?>
	</div>
	<div class="col-sm-6 col-md-6 col-lg-6">
    <?= $form->field($model, "description")->textInput(["maxlength" => true]) ?>
	</div>
</div>
<div class="row">
	<div id="transfer4" class="transfer-demo col-sm-12 col-md-12"></div>
</div>
<?= $form
  ->field($model, "permissionList")
  ->hiddenInput(["id" => "permissionList"])
  ->label(false) ?>
<?php ActiveForm::end(); ?>

<?php
$list = json_encode($permissionData);
$itemText = Yii::t("app", "Permissions");
$selectedText = Yii::t("app", "Selected");
$totalText = Yii::t("app", "Total");
$searchText = Yii::t("app", "Search");
$pageScript = <<<JS
var groupDataArray2 = $list;
    var settings4 = {
        "groupDataArray": groupDataArray2,
        "groupItemName": "groupName",
        "groupArrayName": "groupData",
        "itemName": "name",
        "valueName": "value",
        "tabNameText": "$itemText",
        "rightTabNameText": "$selectedText",
        "totalText": "$totalText",
        "searchPlaceholderText": "$searchText",
        "callable": function (items) {
            let str = [];
            jQuery.each(items, function(index, item) {
              str.push(item.name)
            })
            
            $('#permissionList').val(str.join())
        }
    };

    var transfer = $("#transfer4").transfer(settings4);
    // get selected items
    var items = transfer.getSelectedItems()
JS;
$this->registerJs($pageScript);
 ?>
